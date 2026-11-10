<?php

$uloga = $_SESSION['korisnik_uloga'] ?? null;
$ime = $_SESSION['korisnik_ime'] ?? '';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand"><i class="bi bi-heart-pulse"></i> Teretana</span>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <?php if ($uloga === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="/teretana/admin/dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/teretana/admin/korisnici_lista.php">Korisnici</a></li>
                    <li class="nav-item"><a class="nav-link" href="/teretana/admin/izvestaji.php">Izveštaji</a></li>

                <?php elseif ($uloga === 'trener'): ?>
                    <li class="nav-item"><a class="nav-link" href="/teretana/trener/dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/teretana/trener/termini_lista.php">Moji termini</a></li>

                <?php elseif ($uloga === 'clan'): ?>
                    <li class="nav-item"><a class="nav-link" href="/teretana/clan/dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/teretana/clan/termini_lista.php">Termini</a></li>
                    <li class="nav-item"><a class="nav-link" href="/teretana/clan/moje_rezervacije.php">Moje rezervacije</a></li>
                <?php endif; ?>
            </ul>
            <span class="navbar-text text-light me-3">
                <i class="bi bi-person-circle"></i> <?= htmlspecialchars($ime) ?> (<?= htmlspecialchars($uloga) ?>)
            </span>
            <a href="/teretana/logout.php" class="btn btn-outline-light btn-sm">Odjava</a>
        </div>
    </div>
</nav>
