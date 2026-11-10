<?php
require_once '../includes/auth.php';
proveriUlogu('trener');

require_once '../config/db.php';

$trener_id = $_SESSION['korisnik_id'];

// Trener vidi samo SVOJE termine - filtriramo po trener_id iz sesije
$brojTermina = $pdo->prepare("SELECT COUNT(*) FROM termini WHERE trener_id = :id");
$brojTermina->execute(['id' => $trener_id]);
$brojTermina = $brojTermina->fetchColumn();

// Broj prijavljenih na sve njegove termine ukupno
$brojPrijava = $pdo->prepare("
    SELECT COUNT(*) FROM rezervacije r
    JOIN termini t ON r.trening_id = t.id
    WHERE t.trener_id = :id AND r.status = 'potvrdjena'
");
$brojPrijava->execute(['id' => $trener_id]);
$brojPrijava = $brojPrijava->fetchColumn();

// Sledeći termini (od danas nadalje) - LIMIT 5 znaci da ne prikazuje 
// vise od 5
$sledeciTermini = $pdo->prepare("
    SELECT t.*, COUNT(r.id) AS prijavljeni
    FROM termini t
    LEFT JOIN rezervacije r ON r.trening_id = t.id AND r.status = 'potvrdjena'
    WHERE t.trener_id = :id AND t.datum >= CURDATE()
    GROUP BY t.id
    ORDER BY t.datum ASC, t.vreme_pocetka ASC
    LIMIT 5
");
$sledeciTermini->execute(['id' => $trener_id]);
$sledeciTermini = $sledeciTermini->fetchAll(PDO::FETCH_ASSOC);

$naslov_stranice = "Dashboard";
require_once '../includes/header.php';
?>

<div class="container mt-3 mb-3">
    <a href="/teretana/home.php" class="btn btn-dark rounded-pill px-4 py-2 shadow-sm d-inline-flex align-items-center gap-2 fw-bold">
        <i class="bi bi-arrow-left text-info"></i>
        <i class="bi bi-house-door-fill text-info"></i>
        <span>Vrati se na Početnu</span>
    </a>
</div>

<h2 class="mb-4">Dobrodošao, <?= htmlspecialchars($_SESSION['korisnik_ime']) ?></h2>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <i class="bi bi-calendar-event fs-1 text-primary"></i>
                <h3 class="mt-2"><?= $brojTermina ?></h3>
                <p class="text-muted mb-0">Mojih termina</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <i class="bi bi-people fs-1 text-success"></i>
                <h3 class="mt-2"><?= $brojPrijava ?></h3>
                <p class="text-muted mb-0">Ukupno prijavljenih</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center shadow-sm d-flex align-items-center justify-content-center">
            <div class="card-body">
                <a href="termin_dodaj.php" class="btn btn-success"><i class="bi bi-plus-lg"></i> Dodaj novi termin</a>
            </div>
        </div>
    </div>
</div>

<h4 class="mb-3">Moji sledeći termini</h4>
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Naziv</th>
                    <th>Datum</th>
                    <th>Vreme</th>
                    <th>Popunjenost</th>
                    <th class="text-end">Akcije</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($sledeciTermini)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Nemaš zakazanih termina. <a href="termin_dodaj.php">Dodaj prvi termin</a>.</td></tr>
                <?php endif; ?>
                <?php foreach ($sledeciTermini as $t): ?>
                <tr>
                    <td><?= htmlspecialchars($t['naziv']) ?></td>
                    <td><?= date('d.m.Y', strtotime($t['datum'])) ?></td>
                    <td><?= substr($t['vreme_pocetka'], 0, 5) ?> - <?= substr($t['vreme_zavrsetka'], 0, 5) ?></td>
                    <td><?= $t['prijavljeni'] ?>/<?= $t['max_polaznika'] ?></td>
                    <td class="text-end">
                        <a href="termin_view.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">
    <a href="termini_lista.php" class="btn btn-outline-secondary">Vidi sve moje termine</a>
</div>

<?php require_once '../includes/footer.php'; ?>
