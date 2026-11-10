<?php
require_once '../includes/auth.php';
proveriUlogu('trener');

require_once '../config/db.php';

$id = (int) ($_GET['id'] ?? 0);
$trener_id = $_SESSION['korisnik_id'];

$upit = $pdo->prepare("SELECT * FROM termini WHERE id = :id AND trener_id = :trener_id");
$upit->execute(['id' => $id, 'trener_id' => $trener_id]);
$termin = $upit->fetch(PDO::FETCH_ASSOC);

// trener sme da vidi SAMO svoje termine.
// Bez ove provere, trener bi mogao da promeni ID u URL-u i vidi tuđe termine.
if (!$termin) {
    header("Location: termini_lista.php");
    exit();
}

// Lista članova prijavljenih na ovaj termin
$upit2 = $pdo->prepare("
    SELECT c.ime, c.prezime, c.email, c.telefon, r.datum_kreiranja
    FROM rezervacije r
    JOIN korisnici c ON r.clan_id = c.id
    WHERE r.trening_id = :id AND r.status = 'potvrdjena'
    ORDER BY r.datum_kreiranja ASC
");
$upit2->execute(['id' => $id]);
$prijavljeni = $upit2->fetchAll(PDO::FETCH_ASSOC);

$naslov_stranice = "Pregled termina";
require_once '../includes/header.php';
?>

<a href="termini_lista.php" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Nazad na listu</a>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between">
            <h3><?= htmlspecialchars($termin['naziv']) ?></h3>
            <a href="termin_izmeni.php?id=<?= $termin['id'] ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i> Izmeni</a>
        </div>
        <p class="text-muted"><?= htmlspecialchars($termin['opis'] ?: 'Bez opisa.') ?></p>
        <table class="table table-borderless mb-0">
            <tr><th style="width:180px;">Datum</th><td><?= date('d.m.Y', strtotime($termin['datum'])) ?></td></tr>
            <tr><th>Vreme</th><td><?= substr($termin['vreme_pocetka'], 0, 5) ?> - <?= substr($termin['vreme_zavrsetka'], 0, 5) ?></td></tr>
            <tr><th>Lokacija</th><td><?= htmlspecialchars($termin['lokacija'] ?: '-') ?></td></tr>
            <tr><th>Popunjenost</th><td><?= count($prijavljeni) ?>/<?= $termin['max_polaznika'] ?></td></tr>
        </table>
    </div>
</div>

<h4 class="mb-3">Prijavljeni članovi (<?= count($prijavljeni) ?>)</h4>
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Ime i prezime</th>
                    <th>Email</th>
                    <th>Telefon</th>
                    <th>Prijavljen</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($prijavljeni)): ?>
                    <tr><td colspan="4" class="text-center text-muted py-4">Još niko nije rezervisao ovaj termin.</td></tr>
                <?php endif; ?>
                <?php foreach ($prijavljeni as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['ime'] . ' ' . $p['prezime']) ?></td>
                    <td><?= htmlspecialchars($p['email']) ?></td>
                    <td><?= htmlspecialchars($p['telefon'] ?: '-') ?></td>
                    <td><?= date('d.m.Y H:i', strtotime($p['datum_kreiranja'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
