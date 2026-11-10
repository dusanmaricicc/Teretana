<?php
require_once '../includes/auth.php';
proveriUlogu('clan');

require_once '../config/db.php';

$clan_id = $_SESSION['korisnik_id'];

$upit = $pdo->prepare("
    SELECT t.id AS termin_id, t.naziv, t.datum, t.vreme_pocetka, t.lokacija,
           tr.ime AS trener_ime, tr.prezime AS trener_prezime
    FROM rezervacije r
    JOIN termini t ON r.trening_id = t.id
    JOIN korisnici tr ON t.trener_id = tr.id
    WHERE r.clan_id = :id AND r.status = 'potvrdjena'
    ORDER BY t.datum ASC, t.vreme_pocetka ASC
");
$upit->execute(['id' => $clan_id]);
$rezervacije = $upit->fetchAll(PDO::FETCH_ASSOC);

$naslov_stranice = "Moje rezervacije";
require_once '../includes/header.php';
?>

<h2 class="mb-4">Moje rezervacije</h2>

<?php if (isset($_GET['poruka'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['poruka']) ?></div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Naziv</th>
                    <th>Trener</th>
                    <th>Datum</th>
                    <th>Vreme</th>
                    <th>Lokacija</th>
                    <th class="text-end">Akcije</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rezervacije)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Nemaš aktivnih rezervacija. <a href="termini_lista.php">Pregledaj termine</a>.</td></tr>
                <?php endif; ?>
                <?php foreach ($rezervacije as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['naziv']) ?></td>
                    <td><?= htmlspecialchars($r['trener_ime'] . ' ' . $r['trener_prezime']) ?></td>
                    <td><?= date('d.m.Y', strtotime($r['datum'])) ?></td>
                    <td><?= substr($r['vreme_pocetka'],0,5) ?></td>
                    <td><?= htmlspecialchars($r['lokacija'] ?: '-') ?></td>
                    <td class="text-end">
                        <a href="rezervacija_otkazi.php?id=<?= $r['termin_id'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Otkazati ovu rezervaciju?');">
                           <i class="bi bi-x-circle"></i> Otkaži
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
