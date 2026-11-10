<?php
require_once '../includes/auth.php';
proveriUlogu('trener');

require_once '../config/db.php';

$id = (int) ($_GET['id'] ?? 0);
$trener_id = $_SESSION['korisnik_id'];

// AND trener_id = :trener_id u WHERE delu je ključno - sprečava da trener obriše tuđi termin
// čak i ako pogodi ili promeni ID u URL-u
$upit = $pdo->prepare("DELETE FROM termini WHERE id = :id AND trener_id = :trener_id");
$upit->execute(['id' => $id, 'trener_id' => $trener_id]);

header("Location: termini_lista.php?poruka=Termin+je+obrisan");
exit();
