<?php
require_once '../includes/auth.php';
proveriUlogu('clan');

require_once '../config/db.php';

$clan_id = $_SESSION['korisnik_id'];

// Broj aktivnih (budućih, potvrđenih) rezervacija
$brojRezervacija = $pdo->prepare("
    SELECT COUNT(*) FROM rezervacije r
    JOIN termini t ON r.trening_id = t.id
    WHERE r.clan_id = :id AND r.status = 'potvrdjena' AND t.datum >= CURDATE()
");
$brojRezervacija->execute(['id' => $clan_id]);
$brojRezervacija = $brojRezervacija->fetchColumn();

// Broj dostupnih termina koje član NIJE još rezervisao (samo budući termini)
$brojDostupnih = $pdo->prepare("
    SELECT COUNT(*) FROM termini t
    WHERE t.datum >= CURDATE()
    AND t.id NOT IN (
        SELECT trening_id FROM rezervacije WHERE clan_id = :id AND status = 'potvrdjena'
    )
");
$brojDostupnih->execute(['id' => $clan_id]);
$brojDostupnih = $brojDostupnih->fetchColumn();


$sledeceRezervacije = $pdo->prepare("
    SELECT t.naziv, t.datum, t.vreme_pocetka, t.lokacija, tr.ime AS trener_ime, tr.prezime AS trener_prezime
    FROM rezervacije r
    JOIN termini t ON r.trening_id = t.id
    JOIN korisnici tr ON t.trener_id = tr.id
    WHERE r.clan_id = :id AND r.status = 'potvrdjena' AND t.datum >= CURDATE()
    ORDER BY t.datum ASC, t.vreme_pocetka ASC
    LIMIT 3
");
$sledeceRezervacije->execute(['id' => $clan_id]);
$sledeceRezervacije = $sledeceRezervacije->fetchAll(PDO::FETCH_ASSOC);

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
                <i class="bi bi-calendar-check fs-1 text-success"></i>
                <h3 class="mt-2"><?= $brojRezervacija ?></h3>
                <p class="text-muted mb-0">Mojih rezervacija</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <i class="bi bi-calendar-plus fs-1 text-primary"></i>
                <h3 class="mt-2"><?= $brojDostupnih ?></h3>
                <p class="text-muted mb-0">Dostupnih termina</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center shadow-sm d-flex align-items-center justify-content-center">
            <div class="card-body">
                <a href="termini_lista.php" class="btn btn-primary"><i class="bi bi-search"></i> Pregledaj termine</a>
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
                    <th>Trener</th>
                    <th>Datum</th>
                    <th>Vreme</th>
                    <th>Lokacija</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($sledeceRezervacije)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Nemaš zakazanih termina. <a href="termini_lista.php">Rezerviši jedan</a>.</td></tr>
                <?php endif; ?>
                <?php foreach ($sledeceRezervacije as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['naziv']) ?></td>
                    <td><?= htmlspecialchars($r['trener_ime'] . ' ' . $r['trener_prezime']) ?></td>
                    <td><?= date('d.m.Y', strtotime($r['datum'])) ?></td>
                    <td><?= substr($r['vreme_pocetka'], 0, 5) ?></td>
                    <td><?= htmlspecialchars($r['lokacija'] ?: '-') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
