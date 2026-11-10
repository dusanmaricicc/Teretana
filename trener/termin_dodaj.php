<?php
require_once '../includes/auth.php';
proveriUlogu('trener');

require_once '../config/db.php';

$trener_id = $_SESSION['korisnik_id'];
$greske = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naziv = trim($_POST['naziv']);
    $opis = trim($_POST['opis']);
    $datum = $_POST['datum'];
    $vreme_pocetka = $_POST['vreme_pocetka'];
    $vreme_zavrsetka = $_POST['vreme_zavrsetka'];
    $max_polaznika = (int) $_POST['max_polaznika'];
    $lokacija = trim($_POST['lokacija']);

    //validacija
    if (empty($naziv) || empty($datum) || empty($vreme_pocetka) || empty($vreme_zavrsetka)) {
        $greske[] = "Naziv, datum i vreme su obavezni.";
    }
    // Datum ne sme biti u prošlosti
    if (!empty($datum) && strtotime($datum) < strtotime(date('Y-m-d'))) {
        $greske[] = "Datum termina ne može biti u prošlosti.";
    }
    // Vreme završetka mora biti posle vremena početka
    if (!empty($vreme_pocetka) && !empty($vreme_zavrsetka) && $vreme_zavrsetka <= $vreme_pocetka) {
        $greske[] = "Vreme završetka mora biti posle vremena početka.";
    }
    if ($max_polaznika < 1) {
        $greske[] = "Maksimalan broj polaznika mora biti bar 1.";
    }

    if (empty($greske)) {
        $upit = $pdo->prepare("
            INSERT INTO termini (trener_id, naziv, opis, datum, vreme_pocetka, vreme_zavrsetka, max_polaznika, lokacija)
            VALUES (:trener_id, :naziv, :opis, :datum, :vp, :vz, :max, :lokacija)
        ");
        $upit->execute([
            'trener_id' => $trener_id, 'naziv' => $naziv, 'opis' => $opis,
            'datum' => $datum, 'vp' => $vreme_pocetka, 'vz' => $vreme_zavrsetka,
            'max' => $max_polaznika, 'lokacija' => $lokacija
        ]);

        header("Location: termini_lista.php?poruka=Termin+je+dodat");
        exit();
    }
}

$naslov_stranice = "Dodaj termin";
require_once '../includes/header.php';
?>

<h2 class="mb-4">Dodaj novi termin</h2>

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
                <input type="text" name="naziv" class="form-control" placeholder="npr. Grupni trening - Zumba" value="<?= htmlspecialchars($_POST['naziv'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Opis</label>
                <textarea name="opis" class="form-control" rows="3"><?= htmlspecialchars($_POST['opis'] ?? '') ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Datum</label>
                    <input type="date" name="datum" class="form-control" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_POST['datum'] ?? '') ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Vreme početka</label>
                    <input type="time" name="vreme_pocetka" class="form-control" value="<?= htmlspecialchars($_POST['vreme_pocetka'] ?? '') ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Vreme završetka</label>
                    <input type="time" name="vreme_zavrsetka" class="form-control" value="<?= htmlspecialchars($_POST['vreme_zavrsetka'] ?? '') ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Maksimalan broj polaznika</label>
                    <input type="number" name="max_polaznika" class="form-control" min="1" value="<?= htmlspecialchars($_POST['max_polaznika'] ?? '10') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Lokacija / sala</label>
                    <input type="text" name="lokacija" class="form-control" placeholder="npr. Sala 1" value="<?= htmlspecialchars($_POST['lokacija'] ?? '') ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-success">Sačuvaj termin</button>
            <a href="termini_lista.php" class="btn btn-outline-secondary">Otkaži</a>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
