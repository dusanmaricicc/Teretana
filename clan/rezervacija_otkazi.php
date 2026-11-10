<?php
require_once '../includes/auth.php';
proveriUlogu('clan');

require_once '../config/db.php';

$clan_id = $_SESSION['korisnik_id'];
$trening_id = (int) ($_GET['id'] ?? 0);

// AND clan_id = :clan_id sprečava da član otkoaže tuđu rezervaciju menjanjem ID-a u URL-u
$upit = $pdo->prepare("DELETE FROM rezervacije WHERE trening_id = :trening_id AND clan_id = :clan_id");
$upit->execute(['trening_id' => $trening_id, 'clan_id' => $clan_id]);

header("Location: moje_rezervacije.php?poruka=Rezervacija je otkazana");
exit();
