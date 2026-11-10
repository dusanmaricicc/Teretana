<?php
require_once '../includes/auth.php';
proveriUlogu('admin');

require_once '../config/db.php';

$id = (int) ($_GET['id'] ?? 0);

// Admin ne sme sam sebe da obriše dok je ulogovan - čest bag
if ($id === (int) $_SESSION['korisnik_id']) {
    header("Location: korisnici_lista.php?poruka=Ne+možeš+obrisati+sopstveni+nalog");
    exit();
}


$upit = $pdo->prepare("DELETE FROM korisnici WHERE id = :id");
$upit->execute(['id' => $id]);

header("Location: korisnici_lista.php?poruka=Korisnik+je+obrisan");
exit();
