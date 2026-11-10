<?php
// Ovo je početna straana - ona je vidljiva svima, ne zahteva prijavu.
session_start();

$ulogovan = isset($_SESSION['korisnik_id']);
$panel_link = 'login.php';
if ($ulogovan) {
    switch ($_SESSION['korisnik_uloga']) {
        case 'admin':
            $panel_link = 'admin/dashboard.php';
            break;
        case 'trener':
            $panel_link = 'trener/dashboard.php';
            break;
        default:
            $panel_link = 'clan/dashboard.php';
    }
}
?>
<!DOCTYPE html>
<html lang="sr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Teretana - Početna</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
    :root {
        --gym-dark: #14181f;
        --gym-accent: #ff5b1f;
    }
    body {
        font-family: "Segoe UI", Roboto, Arial, sans-serif;
    }
    .navbar-teretana {
        background-color: var(--gym-dark);
    }
    .navbar-teretana .navbar-brand,
    .navbar-teretana .nav-link {
        color: #fff;
    }
    .navbar-teretana .nav-link:hover {
        color: var(--gym-accent);
    }
    .navbar-teretana .brand-icon {
        color: var(--gym-accent);
    }
    .btn-accent {
        background-color: var(--gym-accent);
        border-color: var(--gym-accent);
        color: #fff;
    }
    .btn-accent:hover {
        background-color: #e04f19;
        border-color: #e04f19;
        color: #fff;
    }
    .hero {
        background:
            linear-gradient(rgba(20,24,31,.82), rgba(20,24,31,.88)),
            repeating-linear-gradient(135deg, #1f2530 0px, #1f2530 40px, #191d26 40px, #191d26 80px);
        color: #fff;
        padding: 110px 0 90px;
    }
    .hero h1 {
        font-weight: 800;
        letter-spacing: .5px;
    }
    .hero .lead {
        color: #d7dae0;
    }
    .hero-icon-row i {
        font-size: 2.6rem;
        color: var(--gym-accent);
        opacity: .9;
    }
    .section-title {
        font-weight: 700;
    }
    .feature-card {
        border: none;
        border-radius: 14px;
        transition: transform .15s ease, box-shadow .15s ease;
        height: 100%;
    }
    .feature-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 28px rgba(0,0,0,.12);
    }
    .feature-icon {
        width: 62px;
        height: 62px;
        border-radius: 50%;
        background: rgba(255,91,31,.12);
        color: var(--gym-accent);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        margin-bottom: 1rem;
    }
    .stats-band {
        background-color: var(--gym-dark);
        color: #fff;
    }
    .stats-band h2 {
        color: var(--gym-accent);
        font-weight: 800;
    }
    .cta-band {
        background: linear-gradient(120deg, var(--gym-accent), #ff8a3d);
        color: #fff;
        border-radius: 18px;
    }
    footer {
        background-color: var(--gym-dark);
        color: #b7bcc7;
    }
    footer a {
        color: #fff;
        text-decoration: none;
    }
    footer a:hover {
        color: var(--gym-accent);
    }
    #onama, #programi, #kontakt {
        scroll-margin-top: 90px;
    }
</style>
</head>
<body>

<!-- navbar -->
<nav class="navbar navbar-expand-lg navbar-teretana sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="home.php">
            <i class="bi bi-lightning-charge-fill brand-icon"></i> Teretana
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#glavniMeni">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="glavniMeni">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="home.php">Početna</a></li>
                <li class="nav-item"><a class="nav-link" href="#onama">O nama</a></li>
                <li class="nav-item"><a class="nav-link" href="#programi">Programi</a></li>
                <li class="nav-item"><a class="nav-link" href="#kontakt">Kontakt</a></li>

                <?php if ($ulogovan): ?>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-light btn-sm" href="<?= htmlspecialchars($panel_link) ?>">
                            <i class="bi bi-speedometer2 me-1"></i> Moj panel
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-accent btn-sm" href="logout.php">Odjava</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-light btn-sm" href="login.php">Prijava</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-accent btn-sm" href="register.php">Registracija</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!--Postani clan,prijavi se-->
<header class="hero text-center">
    <div class="container">
        <div class="hero-icon-row d-flex justify-content-center gap-4 mb-4">
            <i class="bi bi-lightning-charge-fill"></i>
            <i class="bi bi-heart-pulse-fill"></i>
            <i class="bi bi-trophy-fill"></i>
        </div>
        <h1 class="display-4 mb-3">Izgradi snagu. Zakaži trening. Postani bolji.</h1>
        <p class="lead col-lg-8 mx-auto mb-4">
            Rezerviši termine kod naših trenera, prati svoje treninge i postani deo zajednice
            koja svaki dan radi na sebi.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <?php if ($ulogovan): ?>
                <a href="<?= htmlspecialchars($panel_link) ?>" class="btn btn-accent btn-lg px-4">
                    <i class="bi bi-speedometer2 me-1"></i> Idi na moj panel
                </a>
            <?php else: ?>
                <a href="register.php" class="btn btn-accent btn-lg px-4">Postani član</a>
                <a href="login.php" class="btn btn-outline-light btn-lg px-4">Prijavi se</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- o nama -->
<section id="onama" class="py-5">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h2 class="section-title mb-3">O nama</h2>
                <p class="text-muted">
                    Naša teretana okuplja iskusne trenere i motivisane članove pod jednim krovom.
                    Kroz jednostavan sistem za zakazivanje, svaki član može lako da pronađe
                    slobodan termin, rezerviše trening i prati svoju istoriju dolazaka - a treneri
                    imaju pregled svih zakazanih polaznika na jednom mestu.
                </p>
                <ul class="list-unstyled mt-4">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-danger me-2"></i>Online zakazivanje termina 0-24h</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-danger me-2"></i>Sertifikovani, iskusni treneri</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-danger me-2"></i>Pregled sopstvenih rezervacija u svakom trenutku</li>
                </ul>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-4 bg-light rounded-4 text-center h-100">
                            <i class="bi bi-people-fill text-danger fs-1"></i>
                            <div class="fw-bold mt-2">Grupni treninzi</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-4 bg-light rounded-4 text-center h-100">
                            <i class="bi bi-person-arms-up text-danger fs-1"></i>
                            <div class="fw-bold mt-2">Personalni trening</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-4 bg-light rounded-4 text-center h-100">
                            <i class="bi bi-calendar2-week-fill text-danger fs-1"></i>
                            <div class="fw-bold mt-2">Fleksibilni termini</div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</section>

<!-- registruj se,zakazi termin,treniraj i napreduj -->
<section id="programi" class="py-5 bg-light">
    <div class="container py-4">
        <h2 class="section-title text-center mb-2">Kako funkcioniše</h2>
        <p class="text-center text-muted mb-5">Tri koraka do tvog sledećeg treninga</p>
        <div class="row g-4">
            <div class="col-md-4">
                <a href="register.php" class="card feature-card p-4 shadow-sm text-decoration-none text-reset">
                    <div class="feature-icon"><i class="bi bi-person-plus-fill"></i></div>
                    <h5 class="fw-bold">1. Registruj se</h5>
                    <p class="text-muted mb-0">Napravi svoj nalog za par sekundi i postani član naše teretane.</p>
                </a>
            </div>
            <div class="col-md-4">
                <a href="login.php" class="card feature-card p-4 shadow-sm text-decoration-none text-reset">
                    <div class="feature-icon"><i class="bi bi-calendar2-check-fill"></i></div>
                    <h5 class="fw-bold">2. Zakaži termin</h5>
                    <p class="text-muted mb-0">Izaberi trenera i slobodan termin koji ti najviše odgovara.</p>
                </a>
            </div>
            <div class="col-md-4">
                <div class="card feature-card p-4 shadow-sm">
                    <div class="feature-icon"><i class="bi bi-trophy-fill"></i></div>
                    <h5 class="fw-bold">3. Treniraj i napreduj</h5>
                    <p class="text-muted mb-0">Dođi na trening i prati svoj napredak kroz svoj panel.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- statistika -->
<section class="stats-band py-5">
    <div class="container text-center">
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <h2 class="display-6">10+</h2>
                <div>Godina iskustva</div>
            </div>
            <div class="col-6 col-md-3">
                <h2 class="display-6">15+</h2>
                <div>Trenera</div>
            </div>
            <div class="col-6 col-md-3">
                <h2 class="display-6">500+</h2>
                <div>Aktivnih članova</div>
            </div>
            <div class="col-6 col-md-3">
                <h2 class="display-6">1000+</h2>
                <div>Održanih treninga</div>
            </div>
        </div>
    </div>
</section>

<!-- jos jedan panel sa registracijom -->
<section class="py-5">
    <div class="container">
        <div class="cta-band p-5 text-center">
            <h2 class="fw-bold mb-3">Spreman da počneš?</h2>
            <p class="mb-4">Pridruži se danas i rezerviši svoj prvi termin.</p>
            <?php if (!$ulogovan): ?>
                <a href="register.php" class="btn btn-light btn-lg px-4 fw-bold">Registruj se besplatno</a>
            <?php else: ?>
                <a href="<?= htmlspecialchars($panel_link) ?>" class="btn btn-light btn-lg px-4 fw-bold">Idi na moj panel</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- kontakt,u footer-u -->
<footer id="kontakt" class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h5 class="text-white fw-bold mb-3"><i class="bi bi-lightning-charge-fill text-danger"></i> Teretana</h5>
                <p class="mb-0">Tvoje mesto za snagu, izdržljivost i zajednicu.Pocni vec danas.</p>
            </div>
            <div class="col-md-4">
                <h6 class="text-white mb-3">Kontakt</h6>
                <p class="mb-1"><i class="bi bi-geo-alt-fill me-2"></i>Stevana Luburica 15, Beograd</p>
                <p class="mb-1"><i class="bi bi-telephone-fill me-2"></i>+381 64 339 3734</p>
                <p class="mb-0"><i class="bi bi-envelope-fill me-2"></i>info@teretana.rs</p>
            </div>
            <div class="col-md-4">
                <h6 class="text-white mb-3">Brzi linkovi</h6>
                <p class="mb-1"><a href="home.php">Početna</a></p>
                <p class="mb-1"><a href="login.php">Prijava</a></p>
                <p class="mb-0"><a href="register.php">Registracija</a></p>
            </div>
        </div>
        <hr class="border-secondary my-4">
        <p class="text-center mb-0 small">&copy; <?= date('Y') ?> Teretana. Sva prava zadržana.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
