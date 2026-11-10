<?php
session_start();

if (isset($_SESSION['korisnik_id'])) {
    switch ($_SESSION['korisnik_uloga']) {
        case 'admin':
            header("Location: admin/dashboard.php");
            break;
        case 'trener':
            header("Location: trener/dashboard.php");
            break;
        default:
            header("Location: clan/dashboard.php");
    }
} else {
    header("Location: home.php");
}
exit();
