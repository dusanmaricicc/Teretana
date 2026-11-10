<?php
// session_start() mora biti pozvan pre bilo kakvog HTML izlaza
// jer sesija radi preko HTTP zaglavlja (headers)
session_start();

// Provera da li je korisnik uopšte ulogovan
function proveriLogin() {
    if (!isset($_SESSION['korisnik_id'])) {
        header("Location: /teretana/login.php");
        exit(); // da ne bi nastavila skripta da se izrvsrava
    }
}

// Provera da li ulogovani korisnik ima tačno određenu ulogu (admin, trener ili clan)
// Ovo je za sve stranice - poziva se na vrhu admin/trener/clan fajlova
function proveriUlogu($dozvoljena_uloga) {
    proveriLogin(); // prvo mora biti ulogovan

    if ($_SESSION['korisnik_uloga'] !== $dozvoljena_uloga) {
        // Ako korisnik pokuša da otvori npr. admin stranicu a on je clan - vraćamo ga nazad
        header("Location: /teretana/login.php?greska=nemate_pristup");
        exit();
    }
}
