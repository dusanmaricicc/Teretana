<?php
require_once '../includes/auth.php';
proveriUlogu('admin');

require_once '../config/db.php';

// Filter po ulozi preko GET parametra, npr. ?uloga=trener
$filter = $_GET['uloga'] ?? 'svi';

if ($filter === 'svi') {
    $upit = $pdo->query("SELECT * FROM korisnici ORDER BY datum_registracije DESC");
} else {
    
    $upit = $pdo->prepare("SELECT * FROM korisnici WHERE uloga = :uloga ORDER BY datum_registracije DESC");
    $upit->execute(['uloga' => $filter]);
}
$korisnici = $upit->fetchAll(PDO::FETCH_ASSOC);

$naslov_stranice = "Korisnici";
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Upravljanje korisnicima</h2>
    <a href="korisnik_dodaj.php" class="btn btn-success"><i class="bi bi-plus-lg"></i> Dodaj korisnika</a>
</div>

<!-- Filter dugmad -->
<div class="btn-group mb-3">
    <a href="?uloga=svi" class="btn btn-outline-dark <?= $filter === 'svi' ? 'active' : '' ?>">Svi</a>
    <a href="?uloga=admin" class="btn btn-outline-dark <?= $filter === 'admin' ? 'active' : '' ?>">Admini</a>
    <a href="?uloga=trener" class="btn btn-outline-dark <?= $filter === 'trener' ? 'active' : '' ?>">Treneri</a>
    <a href="?uloga=clan" class="btn btn-outline-dark <?= $filter === 'clan' ? 'active' : '' ?>">Članovi</a>
</div>

<?php if (isset($_GET['poruka'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['poruka']) ?></div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Ime i prezime</th>
                    <th>Email</th>
                    <th>Uloga</th>
                    <th>Status</th>
                    <th>Registrovan</th>
                    <th class="text-end">Akcije</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($korisnici)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Nema korisnika za prikaz.</td></tr>
                <?php endif; ?>

                <?php foreach ($korisnici as $k): ?>
                <tr>
                    <td><?= htmlspecialchars($k['ime'] . ' ' . $k['prezime']) ?></td>
                    <td><?= htmlspecialchars($k['email']) ?></td>
                    <td>
                        <?php
                        // Bojenje bedža po ulozi - samo radi preglednosti
                        $boja = ['admin' => 'danger', 'trener' => 'success', 'clan' => 'secondary'][$k['uloga']];
                        ?>
                        <span class="badge bg-<?= $boja ?>"><?= htmlspecialchars($k['uloga']) ?></span>
                    </td>
                    <td>
                        <?php if ($k['aktivan']): ?>
                            <span class="badge bg-success">Aktivan</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Deaktiviran</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('d.m.Y', strtotime($k['datum_registracije'])) ?></td>
                    <td class="text-end">
                        <a href="korisnik_view.php?id=<?= $k['id'] ?>" class="btn btn-sm btn-outline-primary" title="Pregled"><i class="bi bi-eye"></i></a>
                        <a href="korisnik_izmeni.php?id=<?= $k['id'] ?>" class="btn btn-sm btn-outline-warning" title="Izmeni"><i class="bi bi-pencil"></i></a>
                        <a href="korisnik_obrisi.php?id=<?= $k['id'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           title="Obriši"
                           onclick="return confirm('Da li sigurno želiš da obrišeš ovog korisnika? Ova akcija je nepovratna.');">
                           <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
