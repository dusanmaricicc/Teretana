<?php
require_once '../includes/auth.php';
proveriUlogu('admin');

require_once '../config/db.php';


$id = (int) ($_GET['id'] ?? 0);

$upit = $pdo->prepare("SELECT * FROM korisnici WHERE id = :id");
$upit->execute(['id' => $id]);
$korisnik = $upit->fetch(PDO::FETCH_ASSOC);

// Ako korisnik sa tim ID-jem ne postoji, vraćamo se na listu umesto da pokažemo praznu stranicu
if (!$korisnik) {
    header("Location: korisnici_lista.php");
    exit();
}

// Ako je trener, prikazujemo i broj termina koje vodi
$brojTermina = 0;
if ($korisnik['uloga'] === 'trener') {
    $upit2 = $pdo->prepare("SELECT COUNT(*) FROM termini WHERE trener_id = :id");
    $upit2->execute(['id' => $id]);
    $brojTermina = $upit2->fetchColumn();
}

// Ako je clan, prikazujemo broj njegovih rezervacija
$brojRezervacija = 0;
if ($korisnik['uloga'] === 'clan') {
    $upit2 = $pdo->prepare("SELECT COUNT(*) FROM rezervacije WHERE clan_id = :id");
    $upit2->execute(['id' => $id]);
    $brojRezervacija = $upit2->fetchColumn();
}

$naslov_stranice = "Pregled korisnika";
require_once '../includes/header.php';
?>

<a href="korisnici_lista.php" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Nazad na listu</a>

<div class="card shadow-sm" style="max-width: 600px;">
    <div class="card-body">
        <h3><?= htmlspecialchars($korisnik['ime'] . ' ' . $korisnik['prezime']) ?></h3>
        <span class="badge bg-secondary mb-3"><?= htmlspecialchars($korisnik['uloga']) ?></span>

        <table class="table table-borderless">
            <tr><th style="width: 180px;">Email</th><td><?= htmlspecialchars($korisnik['email']) ?></td></tr>
            <tr><th>Telefon</th><td><?= htmlspecialchars($korisnik['telefon'] ?: '-') ?></td></tr>
            <tr><th>Status</th><td><?= $korisnik['aktivan'] ? 'Aktivan' : 'Deaktiviran' ?></td></tr>
            <tr><th>Datum registracije</th><td><?= date('d.m.Y H:i', strtotime($korisnik['datum_registracije'])) ?></td></tr>

            <?php if ($korisnik['uloga'] === 'trener'): ?>
                <tr><th>Broj termina koje vodi</th><td><?= $brojTermina ?></td></tr>
            <?php endif; ?>

            <?php if ($korisnik['uloga'] === 'clan'): ?>
                <tr><th>Broj rezervacija</th><td><?= $brojRezervacija ?></td></tr>
            <?php endif; ?>
        </table>

        <a href="korisnik_izmeni.php?id=<?= $korisnik['id'] ?>" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Izmeni
        </a>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
