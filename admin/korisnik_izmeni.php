<?php
require_once '../includes/auth.php';
proveriUlogu('admin');

require_once '../config/db.php';

$id = (int) ($_GET['id'] ?? 0);
$greske = [];

$upit = $pdo->prepare("SELECT * FROM korisnici WHERE id = :id");
$upit->execute(['id' => $id]);
$korisnik = $upit->fetch(PDO::FETCH_ASSOC);

if (!$korisnik) {
    header("Location: korisnici_lista.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime = trim($_POST['ime']);
    $prezime = trim($_POST['prezime']);
    $email = trim($_POST['email']);
    $telefon = trim($_POST['telefon']);
    $uloga = $_POST['uloga'];
    $aktivan = isset($_POST['aktivan']) ? 1 : 0;
    $nova_lozinka = $_POST['nova_lozinka']; // opciono - samo ako admin želi da je resetuje

    if (empty($ime) || empty($prezime) || empty($email)) {
        $greske[] = "Ime, prezime i email su obavezni.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $greske[] = "Email adresa nije validna.";
    }
    if (!in_array($uloga, ['admin', 'trener', 'clan'])) {
        $greske[] = "Nevažeća uloga.";
    }

    // Provera da email ne pripada nekom DRUGOM korisniku (id != trenutni)
    if (empty($greske)) {
        $provera = $pdo->prepare("SELECT id FROM korisnici WHERE email = :email AND id != :id");
        $provera->execute(['email' => $email, 'id' => $id]);
        if ($provera->fetch()) {
            $greske[] = "Ovaj email već koristi neki drugi korisnik.";
        }
    }

    if (empty($greske)) {
        if (!empty($nova_lozinka)) {
            
            if (strlen($nova_lozinka) < 6) {
                $greske[] = "Nova lozinka mora imati bar 6 karaktera.";
            } else {
                $hash = password_hash($nova_lozinka, PASSWORD_DEFAULT);
                $upit = $pdo->prepare(
                    "UPDATE korisnici SET ime=:ime, prezime=:prezime, email=:email,
                     telefon=:telefon, uloga=:uloga, aktivan=:aktivan, lozinka=:lozinka WHERE id=:id"
                );
                $upit->execute([
                    'ime' => $ime, 'prezime' => $prezime, 'email' => $email, 'telefon' => $telefon,
                    'uloga' => $uloga, 'aktivan' => $aktivan, 'lozinka' => $hash, 'id' => $id
                ]);
            }
        } else {
            // Lozinka se ne menja
            $upit = $pdo->prepare(
                "UPDATE korisnici SET ime=:ime, prezime=:prezime, email=:email,
                 telefon=:telefon, uloga=:uloga, aktivan=:aktivan WHERE id=:id"
            );
            $upit->execute([
                'ime' => $ime, 'prezime' => $prezime, 'email' => $email, 'telefon' => $telefon,
                'uloga' => $uloga, 'aktivan' => $aktivan, 'id' => $id
            ]);
        }

        if (empty($greske)) {
            header("Location: korisnici_lista.php?poruka=Korisnik+je+izmenjen");
            exit();
        }
    }

    // Ako je bilo grešaka, prikazujemo formu opet sa unetim (ne starim) podacima
    $korisnik = array_merge($korisnik, $_POST);
}

$naslov_stranice = "Izmeni korisnika";
require_once '../includes/header.php';
?>

<h2 class="mb-4">Izmeni korisnika</h2>

<?php if (!empty($greske)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($greske as $g): ?><li><?= htmlspecialchars($g) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<div class="card shadow-sm" style="max-width: 600px;">
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Ime</label>
                    <input type="text" name="ime" class="form-control" value="<?= htmlspecialchars($korisnik['ime']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Prezime</label>
                    <input type="text" name="prezime" class="form-control" value="<?= htmlspecialchars($korisnik['prezime']) ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($korisnik['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Telefon</label>
                <input type="text" name="telefon" class="form-control" value="<?= htmlspecialchars($korisnik['telefon'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Uloga</label>
                <select name="uloga" class="form-select">
                    <option value="clan" <?= $korisnik['uloga'] === 'clan' ? 'selected' : '' ?>>Član</option>
                    <option value="trener" <?= $korisnik['uloga'] === 'trener' ? 'selected' : '' ?>>Trener</option>
                    <option value="admin" <?= $korisnik['uloga'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="aktivan" class="form-check-input" id="aktivan" <?= $korisnik['aktivan'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="aktivan">Nalog je aktivan</label>
            </div>
            <div class="mb-3">
                <label class="form-label">Nova lozinka <small class="text-muted">(ostavi prazno ako ne menjaš)</small></label>
                <input type="password" name="nova_lozinka" class="form-control">
            </div>
            <button type="submit" class="btn btn-warning">Sačuvaj izmene</button>
            <a href="korisnici_lista.php" class="btn btn-outline-secondary">Otkaži</a>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
