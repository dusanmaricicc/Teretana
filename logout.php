<?php
session_start();

// $_SESSION = [] briše sve podatke iz trenutne sesije
$_SESSION = [];

// Brišemo i sam sesijski kolačić iz browsera
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// session_destroy() uništava podatke sesije na serveru
session_destroy();

header("Location: login.php");
exit();
