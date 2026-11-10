<?php
require_once '../includes/auth.php';
proveriUlogu('trener');

require_once '../config/db.php';

$trener_id = $_SESSION['korisnik_id'];

$termini = $pdo->prepare("
    SELECT t.*, COUNT(r.id) AS prijavljeni
    FROM termini t
    LEFT JOIN rezervacije r ON r.trening_id = t.id AND r.status = 'potvrdjena'
    WHERE t.trener_id = :id
    GROUP BY t.id
    ORDER BY t.datum DESC, t.vreme_pocetka DESC
");
$termini->execute(['id' => $trener_id]);
$termini = $termini->fetchAll(PDO::FETCH_ASSOC);

$naslov_stranice = "Moji termini";
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Moji termini</h2>
    <a href="termin_dodaj.php" class="btn btn-success"><i class="bi bi-plus-lg"></i> Dodaj termin</a>
</div>

<?php if (isset($_GET['poruka'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['poruka']) ?></div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Naziv</th>
                    <th>Datum</th>
                    <th>Vreme</th>
                    <th>Lokacija</th>
                    <th>Popunjenost</th>
                    <th class="text-end">Akcije</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($termini)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Još nemaš dodatih termina.</td></tr>
                <?php endif; ?>
                <?php foreach ($termini as $t): ?>
                <?php $prosao = strtotime($t['datum']) < strtotime(date('Y-m-d')); ?>
                <tr class="<?= $prosao ? 'text-muted' : '' ?>">
                    <td><?= htmlspecialchars($t['naziv']) ?> <?php if ($prosao): ?><span class="badge bg-light text-dark border">prošao</span><?php endif; ?></td>
                    <td><?= date('d.m.Y', strtotime($t['datum'])) ?></td>
                    <td><?= substr($t['vreme_pocetka'], 0, 5) ?> - <?= substr($t['vreme_zavrsetka'], 0, 5) ?></td>
                    <td><?= htmlspecialchars($t['lokacija'] ?: '-') ?></td>
                    <td>
                        <?= $t['prijavljeni'] ?>/<?= $t['max_polaznika'] ?>
                        <?php if ($t['prijavljeni'] >= $t['max_polaznika']): ?>
                            <span class="badge bg-danger">popunjeno</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <a href="termin_view.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary" title="Pregled"><i class="bi bi-eye"></i></a>
                        <a href="termin_izmeni.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-warning" title="Izmeni"><i class="bi bi-pencil"></i></a>
                        <a href="termin_obrisi.php?id=<?= $t['id'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           title="Obriši"
                           onclick="return confirm('Obrisati ovaj termin? Sve rezervacije za njega će takođe biti obrisane.');">
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
