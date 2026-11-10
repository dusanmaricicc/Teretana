<?php
// Podaci za konekciju
$host = "localhost";
$db_name = "teretana";
$username = "root";   
$password = "";       
                      

try {
    // PDO (PHP Data Objects) -koristio sam ovaj interfejs za povezivanje sa
    // bazom
    
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db_name;charset=utf8mb4",
        $username,
        $password
    );

    // Postavljam da PDO baca izuzetke (exceptions) kada dodje do neke greske
    // (prekida)
    // umesto da nastavi dalje - lakše je pronaći greške
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Ako konekcija ne uspe, zaustavljamo skriptu uz poruku
    die("Greška pri konekciji na bazu: " . $e->getMessage());
}
