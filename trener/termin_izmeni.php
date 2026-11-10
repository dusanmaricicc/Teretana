<?php
require_once '../includes/auth.php';
proveriUlogu('trener');

require_once '../config/db.php';

$id = (int) ($_GET['id'] ?? 0);
$trener_id = $_SESSION['korisnik_id'];
$greske = [];

$upit = $pdo->prepare("SELECT * FROM termini WHERE id = :id AND trener_id = :trener_id");
$upit->execute(['id' => $id, 'trener_id' => $trener_id]);
$termin = $upit->fetch(PDO::FETCH_ASSOC);

if (!$termin) {
    header("Location: termini_lista.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naziv = trim($_POST['naziv']);
    $opis = trim($_POST['opis']);
    $datum = $_POST['datum'];
    $vreme_pocetka = $_POST['vreme_pocetka'];
    $vreme_zavrsetka = $_POST['vreme_zavrsetka'];
    $max_polaznika = (int) $_POST['max_polaznika'];
    $lokacija = trim($_POST['lokacija']);

    if (empty($naziv) || empty($datum) || empty($vreme_pocetka) || empty($vreme_zavrsetka)) {
        $greske[] = "Naziv, datum i vreme su obavezni.";
    }
    if ($vreme_zavrsetka <= $vreme_pocetka) {
        $greske[] = "Vreme završetka mora biti posle vremena početka.";
    }
    // Ne dozvoljavamo da max_polaznika bude manji od broja već prijavljenih
    $brojPrijavljenih = $pdo->prepare("SELECT COUNT(*) FROM rezervacije WHERE trening_id = :id AND status = 'potvrdjena'");
    $brojPrijavljenih->execute(['id' => $id]);
    $brojPrijavljenih = $brojPrijavljenih->fetchColumn();
    if ($max_polaznika < $brojPrijavljenih) {
        $greske[] = "Ne možeš smanjiti kapacitet ispod broja već prijavljenih ($brojPrijavljenih).";
    }

    if (empty($greske)) {
        $upit = $pdo->prepare("
            UPDATE termini SET naziv=:naziv, opis=:opis, datum=:datum,
            vreme_pocetka=:vp, vreme_zavrsetka=:vz, max_polaznika=:max, lokacija=:lokacija
            WHERE id=:id AND trener_id=:trener_id
        ");
        $upit->execute([
            'naziv' => $naziv, 'opis' => $opis, 'datum' => $datum,
            'vp' => $vreme_pocetka, 'vz' => $vreme_zavrsetka, 'max' => $max_polaznika,
            'lokacija' => $lokacija, 'id' => $id, 'trener_id' => $trener_id
        ]);

        header("Location: termini_lista.php?poruka=Termin+je+izmenjen");
        exit();
    }

    $termin = array_merge($termin, $_POST);
}

$naslov_stranice = "Izmeni termin";
require_once '../includes/header.php';
?>

<h2 class="mb-4">Izmeni termin</h2>

<?php if (!empty($greske)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($greske as $g): ?><li><?= htmlspecialchars($g) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="card shadow-sm" style="max-width: 600px;">
    <div class="card-body">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Naziv treninga</label>
                <input type="text" name="naziv" class="form-control" value="<?= htmlspecialchars($termin['naziv']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Opis</label>
                <textarea name="opis" class="form-control" rows="3"><?= htmlspecialchars($termin['opis'] ?? '') ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Datum</label>
                    <input type="date" name="datum" class="form-control" value="<?= htmlspecialchars($termin['datum']) ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Vreme početka</label>
                    <input type="time" name="vreme_pocetka" class="form-control" value="<?= htmlspecialchars(substr($termin['vreme_pocetka'],0,5)) ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Vreme završetka</label>
                    <input type="time" name="vreme_zavrsetka" class="form-control" value="<?= htmlspecialchars(substr($termin['vreme_zavrsetka'],0,5)) ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Maksimalan broj polaznika</label>
                    <input type="number" name="max_polaznika" class="form-control" min="1" value="<?= htmlspecialchars($termin['max_polaznika']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Lokacija / sala</label>
                    <input type="text" name="lokacija" class="form-control" value="<?= htmlspecialchars($termin['lokacija'] ?? '') ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-warning">Sačuvaj izmene</button>
            <a href="termini_lista.php" class="btn btn-outline-secondary">Otkaži</a>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
