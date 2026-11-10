<?php
require_once '../includes/auth.php';
proveriUlogu('admin'); // samo admin sme na ovu stranicu

require_once '../config/db.php';

// Prebrojavanje zapisa za statistiku - COUNT(*) je bolje koristiti nego 
// fetchAll() + count()
// jer bazu pitamo samo za broj, ne vučemo sve podatke bez potrebe
$brojClanova = $pdo->query("SELECT COUNT(*) FROM korisnici WHERE uloga = 'clan'")->fetchColumn();
$brojTrenera = $pdo->query("SELECT COUNT(*) FROM korisnici WHERE uloga = 'trener'")->fetchColumn();
$brojTermina = $pdo->query("SELECT COUNT(*) FROM termini")->fetchColumn();
$brojRezervacija = $pdo->query("SELECT COUNT(*) FROM rezervacije WHERE status = 'potvrdjena'")->fetchColumn();

$naslov_stranice = "Dashboard";
require_once '../includes/header.php';
?>

<div class="container mt-3 mb-3">
    <a href="/teretana/home.php" class="btn btn-dark rounded-pill px-4 py-2 shadow-sm d-inline-flex align-items-center gap-2 fw-bold">
        <i class="bi bi-arrow-left text-info"></i>
        <i class="bi bi-house-door-fill text-info"></i>
        <span>Vrati se na Početnu</span>
    </a>
</div>

<h2 class="mb-4">Admin Dashboard</h2>

<div class="row g-3">
    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <i class="bi bi-people fs-1 text-primary"></i>
                <h3 class="mt-2"><?= $brojClanova ?></h3>
                <p class="text-muted mb-0">Članova</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <i class="bi bi-person-badge fs-1 text-success"></i>
                <h3 class="mt-2"><?= $brojTrenera ?></h3>
                <p class="text-muted mb-0">Trenera</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <i class="bi bi-calendar-event fs-1 text-warning"></i>
                <h3 class="mt-2"><?= $brojTermina ?></h3>
                <p class="text-muted mb-0">Termina ukupno</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <i class="bi bi-check-circle fs-1 text-info"></i>
                <h3 class="mt-2"><?= $brojRezervacija ?></h3>
                <p class="text-muted mb-0">Aktivnih rezervacija</p>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="korisnici_lista.php" class="btn btn-primary"><i class="bi bi-people"></i> Upravljaj korisnicima</a>
    <a href="izvestaji.php" class="btn btn-outline-secondary"><i class="bi bi-graph-up"></i> Izveštaji</a>
</div>

<?php require_once '../includes/footer.php'; ?>
