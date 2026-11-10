<?php
require_once '../includes/auth.php';
proveriUlogu('clan');

require_once '../config/db.php';

$clan_id = $_SESSION['korisnik_id'];
$trening_id = (int) ($_POST['trening_id'] ?? 0);

$upit = $pdo->prepare("SELECT * FROM termini WHERE id = :id");
$upit->execute(['id' => $trening_id]);
$termin = $upit->fetch(PDO::FETCH_ASSOC);

if (!$termin) {
    header("Location: termini_lista.php");
    exit();
}


$brojPrijavljenih = $pdo->prepare("SELECT COUNT(*) FROM rezervacije WHERE trening_id = :id AND status = 'potvrdjena'");
$brojPrijavljenih->execute(['id' => $trening_id]);
$brojPrijavljenih = $brojPrijavljenih->fetchColumn();

if ($brojPrijavljenih >= $termin['max_polaznika']) {
    header("Location: termin_detalji.php?id=$trening_id&greska=Nažalost, termin se upravo popunio.");
    exit();
}

if (strtotime($termin['datum']) < strtotime(date('Y-m-d'))) {
    header("Location: termin_detalji.php?id=$trening_id&greska=Ne možeš rezervisati termin koji je prošao.");
    exit();
}

try {
    // UNIQUE KEY (clan_id, trening_id) u bazi je resenje protiv duplog 
    // klika/rezervacije -
    // ako iz nekog razloga ovde promakne, baza sama odbija duplikat i 
    // baca izuzetak koji hvatamo ispod
    $upit = $pdo->prepare("INSERT INTO rezervacije (clan_id, trening_id, status) VALUES (:clan_id, :trening_id, 'potvrdjena')");
    $upit->execute(['clan_id' => $clan_id, 'trening_id' => $trening_id]);

    header("Location: termin_detalji.php?id=$trening_id&poruka=Uspešno si rezervisao/la termin!");
} catch (PDOException $e) {
    header("Location: termin_detalji.php?id=$trening_id&greska=Već si rezervisao/la ovaj termin.");
}
exit();
