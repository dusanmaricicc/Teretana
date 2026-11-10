<?php
require_once 'config/db.php';
session_start();

$greska = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $lozinka = $_POST['lozinka'];

    if (empty($email) || empty($lozinka)) {
        $greska = "Unesite email i lozinku.";
    } else {
        // Tražim korisnika po emailu 
        $upit = $pdo->prepare("SELECT * FROM korisnici WHERE email = :email");
        $upit->execute(['email' => $email]);
        $korisnik = $upit->fetch(PDO::FETCH_ASSOC);

        // password_verify upoređuje unetu lozinku sa sačuvanim hash-om
        if ($korisnik && password_verify($lozinka, $korisnik['lozinka'])) {

            if ($korisnik['aktivan'] == 0) {
                $greska = "Vaš nalog je deaktiviran. Obratite se administratoru.";
            } else {
                
                session_regenerate_id(true);

                $_SESSION['korisnik_id'] = $korisnik['id'];
                $_SESSION['korisnik_ime'] = $korisnik['ime'] . ' ' . $korisnik['prezime'];
                $_SESSION['korisnik_uloga'] = $korisnik['uloga'];

                // Preusmeravanje na odgovarajući panel prema ulozi
                switch ($korisnik['uloga']) {
                    case 'admin':
                    header("Location: admin/dashboard.php");
                    break;
                    case 'trener':
                    header("Location: trener/dashboard.php");
                    break;
                    default:
                    header("Location: clan/dashboard.php");
                }
                exit();
            }
        } else {
            $greska = "Pogrešan email ili lozinka.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Prijava - Teretana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container pt-3">
        <a href="home.php" class="btn btn-outline-primary rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2 fw-semibold">
            <i class="bi bi-arrow-left"></i>
            <i class="bi bi-house-door-fill"></i>
            <span>Početna strana</span>
        </a>
    </div>    
    <div class="container" style="max-width: 420px; margin-top: 100px;">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="mb-4 text-center">Prijava</h3>

                <?php if ($greska): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($greska) ?></div>
                <?php endif; ?>

                <?php if (isset($_GET['greska']) && $_GET['greska'] === 'nemate_pristup'): ?>
                    <div class="alert alert-warning">Nemate pristup toj stranici.</div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        class="form-control"
                        pattern="[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}"
                        oninvalid="this.setCustomValidity('Unesite ispravan format email adrese.')"
                        oninput="this.setCustomValidity('')"
                        required
                        >
                        <div class="mb-3">
                            <label class="form-label">Lozinka</label>
                            <input type="password" name="lozinka" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Prijavi se</button>
                    </form>
                    <p class="text-center mt-3 mb-0">Nemate nalog? <a href="register.php">Registrujte se</a></p>
                </div>
            </div>
        </div>
    </body>
    </html>
