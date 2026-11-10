-- ============================================
-- Baza podataka: sistem za rezervaciju termina u teretani
-- ============================================

CREATE DATABASE IF NOT EXISTS teretana CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE teretana;

-- --------------------------------------------
-- Tabela: korisnici
-- Sadrži sve tri uloge (admin, trener, clan) razdvojene kolonom "uloga"
-- --------------------------------------------
CREATE TABLE korisnici (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ime VARCHAR(50) NOT NULL,
    prezime VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    lozinka VARCHAR(255) NOT NULL,          -- čuvamo hash, nikad plain-text lozinku
    uloga ENUM('admin', 'trener', 'clan') NOT NULL DEFAULT 'clan',
    telefon VARCHAR(20),
    aktivan TINYINT(1) NOT NULL DEFAULT 1,  -- admin može da deaktivira nalog umesto brisanja
    datum_registracije TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------
-- Tabela: termini
-- Termine kreira trener (trener_id je strani ključ ka korisnicima)
-- --------------------------------------------
CREATE TABLE termini (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trener_id INT NOT NULL,
    naziv VARCHAR(100) NOT NULL,
    opis TEXT,
    datum DATE NOT NULL,
    vreme_pocetka TIME NOT NULL,
    vreme_zavrsetka TIME NOT NULL,
    max_polaznika INT NOT NULL DEFAULT 10,
    lokacija VARCHAR(100),
    datum_kreiranja TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trener_id) REFERENCES korisnici(id) ON DELETE CASCADE
);

-- --------------------------------------------
-- Tabela: rezervacije
-- Veže člana (clan_id) sa terminom (trening_id)
-- --------------------------------------------
CREATE TABLE rezervacije (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clan_id INT NOT NULL,
    trening_id INT NOT NULL,
    status ENUM('potvrdjena', 'otkazana') NOT NULL DEFAULT 'potvrdjena',
    datum_kreiranja TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (clan_id) REFERENCES korisnici(id) ON DELETE CASCADE,
    FOREIGN KEY (trening_id) REFERENCES termini(id) ON DELETE CASCADE,
    UNIQUE KEY jedna_rezervacija_po_terminu (clan_id, trening_id) -- član ne može duplo da rezerviše isti termin
);