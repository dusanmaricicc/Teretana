<?php
require_once 'config/db.php';
session_start();

$greske = [];
$uspeh = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // trim() uklanja razmake sa početka/kraja da neko slučajno ne unese " ime "
    $ime = trim($_POST['ime']);
    $prezime = trim($_POST['prezime']);
    $email = trim($_POST['email']);
    $lozinka = $_POST['lozinka'];
    $lozinka_potvrda = $_POST['lozinka_potvrda'];
    $telefon = trim($_POST['telefon']);

    // validaccija podataka
    if (empty($ime) || empty($prezime) || empty($email) || empty($lozinka)) {
        $greske[] = "Sva obavezna polja moraju biti popunjena.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $greske[] = "Email adresa nije validna.";
    }
    if (strlen($lozinka) < 6) {
        $greske[] = "Lozinka mora imati bar 6 karaktera.";
    }
    if ($lozinka !== $lozinka_potvrda) {
        $greske[] = "Lozinke se ne poklapaju.";
    }

    // Proveravam da li email već postoji u bazi 
    
    if (empty($greske)) {
        $upit = $pdo->prepare("SELECT id FROM korisnici WHERE email = :email");
        $upit->execute(['email' => $email]);
        if ($upit->fetch()) {
            $greske[] = "Nalog sa ovim emailom već postoji.";
        }
    }

    // upisaivanje u bazu
    if (empty($greske)) {
        // lozinku nikad ne čuvamo kao čist tekst,vec u hash kodu
        $hash = password_hash($lozinka, PASSWORD_DEFAULT);

        $upit = $pdo->prepare(
            "INSERT INTO korisnici (ime, prezime, email, lozinka, uloga, telefon)
            VALUES (:ime, :prezime, :email, :lozinka, 'clan', :telefon)"
        );
        $upit->execute([
            'ime' => $ime,
            'prezime' => $prezime,
            'email' => $email,
            'lozinka' => $hash,
            'telefon' => $telefon
        ]);

        $uspeh = true;
    }
}
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Registracija - Teretana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container pt-3">
        <a href="index.php" class="btn btn-outline-primary rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2 fw-semibold">
            <i class="bi bi-arrow-left"></i>
            <i class="bi bi-house-door-fill"></i>
            <span>Početna strana</span>
        </a>
    </div>    
    <div class="container" style="max-width: 500px; margin-top: 60px;">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="mb-4 text-center">Registracija člana</h3>

                <?php if ($uspeh): ?>
                    <div class="alert alert-success">
                        Uspešno ste se registrovali! <a href="login.php">Prijavite se ovde</a>.
                    </div>
                <?php else: ?>

                    <?php if (!empty($greske)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($greske as $g): ?>
                                    <li><?= htmlspecialchars($g) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Ime</label>
                            <input type="text" name="ime" class="form-control"
                            pattern="[A-ZČĆŽŠĐ][a-zčćžšđ]+"
                            value="<?= htmlspecialchars($_POST['ime'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prezime</label>
                            <input type="text" name="prezime" class="form-control"
                            pattern="[A-ZČĆŽŠĐ][a-zčćžšđ]+"
                            value="<?= htmlspecialchars($_POST['prezime'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                            pattern="[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Telefon</label>
                            <input type="text" name="telefon" class="form-control"
                            pattern="06[0-9]{8}"
                            value="<?= htmlspecialchars($_POST['telefon'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lozinka</label>
                            <input type="password" name="lozinka" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Potvrda lozinke</label>
                            <input type="password" name="lozinka_potvrda" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Registruj se</button>
                    </form>
                    <p class="text-center mt-3 mb-0">Već imate nalog? <a href="login.php">Prijavite se</a></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
