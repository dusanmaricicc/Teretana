<?php
require_once '../includes/auth.php';
proveriUlogu('admin');

require_once '../config/db.php';

$greske = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime = trim($_POST['ime']);
    $prezime = trim($_POST['prezime']);
    $email = trim($_POST['email']);
    $telefon = trim($_POST['telefon']);
    $uloga = $_POST['uloga'];
    $lozinka = $_POST['lozinka'];

    // Validacija - ista logika kao u register.php, plus provera da je uloga jedna od dozvoljenih
    if (empty($ime) || empty($prezime) || empty($email) || empty($lozinka)) {
        $greske[] = "Sva obavezna polja moraju biti popunjena.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $greske[] = "Email adresa nije validna.";
    }
    if (!in_array($uloga, ['admin', 'trener', 'clan'])) {
        $greske[] = "Nevažeća uloga.";
    }
    if (strlen($lozinka) < 6) {
        $greske[] = "Lozinka mora imati bar 6 karaktera.";
    }

    if (empty($greske)) {
        $upit = $pdo->prepare("SELECT id FROM korisnici WHERE email = :email");
        $upit->execute(['email' => $email]);
        if ($upit->fetch()) {
            $greske[] = "Korisnik sa ovim emailom već postoji.";
        }
    }

    if (empty($greske)) {
        $hash = password_hash($lozinka, PASSWORD_DEFAULT);
        $upit = $pdo->prepare(
            "INSERT INTO korisnici (ime, prezime, email, lozinka, uloga, telefon)
             VALUES (:ime, :prezime, :email, :lozinka, :uloga, :telefon)"
        );
        $upit->execute([
            'ime' => $ime, 'prezime' => $prezime, 'email' => $email,
            'lozinka' => $hash, 'uloga' => $uloga, 'telefon' => $telefon
        ]);

        // Redirect posle uspešnog unosa  -
        // sprečava da refresh stranice da doda istog korisnika dva puta
        header("Location: korisnici_lista.php?poruka=Korisnik+je+uspešno+dodat");
        exit();
    }
}

$naslov_stranice = "Dodaj korisnika";
require_once '../includes/header.php';
?>

<h2 class="mb-4">Dodaj novog korisnika</h2>

<?php if (!empty($greske)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($greske as $g): ?><li><?= htmlspecialchars($g) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card shadow-sm" style="max-width: 600px;">
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Ime</label>
                    <input type="text" name="ime" class="form-control" value="<?= htmlspecialchars($_POST['ime'] ?? '') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Prezime</label>
                    <input type="text" name="prezime" class="form-control" value="<?= htmlspecialchars($_POST['prezime'] ?? '') ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Telefon</label>
                <input type="text" name="telefon" class="form-control" value="<?= htmlspecialchars($_POST['telefon'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Uloga</label>
                <select name="uloga" class="form-select" required>
                    <option value="clan">Član</option>
                    <option value="trener">Trener</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Lozinka</label>
                <input type="password" name="lozinka" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Sačuvaj</button>
            <a href="korisnici_lista.php" class="btn btn-outline-secondary">Otkaži</a>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
