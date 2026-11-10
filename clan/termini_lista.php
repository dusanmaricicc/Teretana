<?php
require_once '../includes/auth.php';
proveriUlogu('clan');

require_once '../config/db.php';

$clan_id = $_SESSION['korisnik_id'];

// Filter: 'svi' prikazuje sve buduće termine, 'dostupni' samo one koje član nije rezervisao
$filter = $_GET['prikaz'] ?? 'svi';

$osnovni_upit = "
    SELECT t.*, tr.ime AS trener_ime, tr.prezime AS trener_prezime,
           COUNT(r.id) AS prijavljeni,
           MAX(CASE WHEN r.clan_id = :clan_id THEN 1 ELSE 0 END) AS ja_rezervisao
    FROM termini t
    JOIN korisnici tr ON t.trener_id = tr.id
    LEFT JOIN rezervacije r ON r.trening_id = t.id AND r.status = 'potvrdjena'
    WHERE t.datum >= CURDATE()
    GROUP BY t.id
";

if ($filter === 'dostupni') {
    
    $osnovni_upit .= " HAVING ja_rezervisao = 0 ";
}
$osnovni_upit .= " ORDER BY t.datum ASC, t.vreme_pocetka ASC";

$upit = $pdo->prepare($osnovni_upit);
$upit->execute(['clan_id' => $clan_id]);
$termini = $upit->fetchAll(PDO::FETCH_ASSOC);

$naslov_stranice = "Termini";
require_once '../includes/header.php';
?>

<h2 class="mb-4">Dostupni termini</h2>

<?php if (isset($_GET['poruka'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['poruka']) ?></div>
<?php endif; ?>
<?php if (isset($_GET['greska'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['greska']) ?></div>
<?php endif; ?>

<div class="btn-group mb-3">
    <a href="?prikaz=svi" class="btn btn-outline-dark <?= $filter === 'svi' ? 'active' : '' ?>">Svi termini</a>
    <a href="?prikaz=dostupni" class="btn btn-outline-dark <?= $filter === 'dostupni' ? 'active' : '' ?>">Koje nisam rezervisao</a>
</div>

<div class="row g-3">
    <?php if (empty($termini)): ?>
        <p class="text-muted">Trenutno nema dostupnih termina.</p>
    <?php endif; ?>

    <?php foreach ($termini as $t): ?>
    <?php $popunjeno = $t['prijavljeni'] >= $t['max_polaznika']; ?>
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title"><?= htmlspecialchars($t['naziv']) ?></h5>
                <p class="text-muted small mb-2">
                    <i class="bi bi-person"></i> <?= htmlspecialchars($t['trener_ime'] . ' ' . $t['trener_prezime']) ?>
                </p>
                <p class="mb-1"><i class="bi bi-calendar"></i> <?= date('d.m.Y', strtotime($t['datum'])) ?></p>
                <p class="mb-1"><i class="bi bi-clock"></i> <?= substr($t['vreme_pocetka'],0,5) ?> - <?= substr($t['vreme_zavrsetka'],0,5) ?></p>
                <p class="mb-2"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($t['lokacija'] ?: '-') ?></p>

                <div class="mt-auto">
                    <p class="mb-2">
                        <span class="badge <?= $popunjeno ? 'bg-danger' : 'bg-success' ?>">
                            <?= $t['prijavljeni'] ?>/<?= $t['max_polaznika'] ?> mesta
                        </span>
                        <?php if ($t['ja_rezervisao']): ?>
                            <span class="badge bg-primary">Rezervisano</span>
                        <?php endif; ?>
                    </p>

                    <a href="termin_detalji.php?id=<?= $t['id'] ?>" class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-eye"></i> Detalji <?= $t['ja_rezervisao'] ? '' : '/ Rezerviši' ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
