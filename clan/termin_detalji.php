<?php
require_once '../includes/auth.php';
proveriUlogu('clan');

require_once '../config/db.php';

$id = (int) ($_GET['id'] ?? 0);
$clan_id = $_SESSION['korisnik_id'];

$upit = $pdo->prepare("
    SELECT t.*, tr.ime AS trener_ime, tr.prezime AS trener_prezime
    FROM termini t
    JOIN korisnici tr ON t.trener_id = tr.id
    WHERE t.id = :id
");
$upit->execute(['id' => $id]);
$termin = $upit->fetch(PDO::FETCH_ASSOC);

if (!$termin) {
    header("Location: termini_lista.php");
    exit();
}

// Broj trenutno prijavljenih
$upit2 = $pdo->prepare("SELECT COUNT(*) FROM rezervacije WHERE trening_id = :id AND status = 'potvrdjena'");
$upit2->execute(['id' => $id]);
$prijavljeni = $upit2->fetchColumn();

// provera da li se ovaj clan vec prijavio na ovaj tremin
$upit3 = $pdo->prepare("SELECT id FROM rezervacije WHERE trening_id = :id AND clan_id = :clan_id AND status = 'potvrdjena'");
$upit3->execute(['id' => $id, 'clan_id' => $clan_id]);
$vecRezervisano = (bool) $upit3->fetch();

$popunjeno = $prijavljeni >= $termin['max_polaznika'];
$prosao = strtotime($termin['datum']) < strtotime(date('Y-m-d'));

$naslov_stranice = "Detalji termina";
require_once '../includes/header.php';
?>

<a href="termini_lista.php" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Nazad na listu</a>

<?php if (isset($_GET['greska'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['greska']) ?></div>
<?php endif; ?>
<?php if (isset($_GET['poruka'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['poruka']) ?></div>
<?php endif; ?>

<div class="card shadow-sm" style="max-width: 600px;">
    <div class="card-body">
        <h3><?= htmlspecialchars($termin['naziv']) ?></h3>
        <p class="text-muted"><?= htmlspecialchars($termin['opis'] ?: 'Bez dodatnog opisa.') ?></p>

        <table class="table table-borderless">
            <tr><th style="width:180px;">Trener</th><td><?= htmlspecialchars($termin['trener_ime'] . ' ' . $termin['trener_prezime']) ?></td></tr>
            <tr><th>Datum</th><td><?= date('d.m.Y', strtotime($termin['datum'])) ?></td></tr>
            <tr><th>Vreme</th><td><?= substr($termin['vreme_pocetka'],0,5) ?> - <?= substr($termin['vreme_zavrsetka'],0,5) ?></td></tr>
            <tr><th>Lokacija</th><td><?= htmlspecialchars($termin['lokacija'] ?: '-') ?></td></tr>
            <tr><th>Slobodna mesta</th><td><?= max(0, $termin['max_polaznika'] - $prijavljeni) ?> od <?= $termin['max_polaznika'] ?></td></tr>
        </table>

        <?php if ($vecRezervisano): ?>
            <?php if (!isset($_GET['poruka'])): ?>
                <div class="alert alert-info">Već si rezervisao/la ovaj termin.</div>
            <?php endif; ?>
            <a href="rezervacija_otkazi.php?id=<?= $termin['id'] ?>" class="btn btn-outline-danger"
               onclick="return confirm('Otkazati rezervaciju za ovaj termin?');">
               <i class="bi bi-x-circle"></i> Otkaži rezervaciju
            </a>
        <?php elseif ($prosao): ?>
            <div class="alert alert-secondary">Ovaj termin je već prošao.</div>
        <?php elseif ($popunjeno): ?>
            <div class="alert alert-warning">Termin je popunjen, nema više slobodnih mesta.</div>
        <?php else: ?>
            <form method="POST" action="rezervisi.php">
                <input type="hidden" name="trening_id" value="<?= $termin['id'] ?>">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Rezerviši termin</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
