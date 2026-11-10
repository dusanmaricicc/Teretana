<?php
// Ovaj fajl se uključuje na vrhu svake stranice (posle provere uloge)

?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($naslov_stranice) ? htmlspecialchars($naslov_stranice) . ' - ' : '' ?>Teretana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/teretana/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php require_once __DIR__ . '/navbar.php'; ?>

<div class="container my-4">
