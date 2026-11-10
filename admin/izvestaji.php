<?php
require_once '../includes/auth.php';
proveriUlogu('admin');

require_once '../config/db.php';

// JOIN spaja tri tabele u jedan rezultat - za svaku rezervaciju dobijamo
// i ime člana, i naziv termina, i ime trenera koji ga vodi
$rezervacije = $pdo->query("
    SELECT r.id, r.status, r.datum_kreiranja,
           c.ime AS clan_ime, c.prezime AS clan_prezime,
           t.naziv AS termin_naziv, t.datum AS termin_datum,
           tr.ime AS trener_ime, tr.prezime AS trener_prezime
    FROM rezervacije r
    JOIN korisnici c ON r.clan_id = c.id
    JOIN termini t ON r.trening_id = t.id
    JOIN korisnici tr ON t.trener_id = tr.id
    ORDER BY r.datum_kreiranja DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Popunjenost termina - GROUP BY grupiše rezervacije po terminu i broji ih (COUNT)
$popunjenost = $pdo->query("
    SELECT t.id, t.naziv, t.datum, t.max_polaznika,
           COUNT(r.id) AS broj_prijavljenih
    FROM termini t
    LEFT JOIN rezervacije r ON r.trening_id = t.id AND r.status = 'potvrdjena'
    GROUP BY t.id
    ORDER BY t.datum DESC
")->fetchAll(PDO::FETCH_ASSOC);

$naslov_stranice = "Izveštaji";
require_once '../includes/header.php';
?>

<h2 class="mb-4">Izveštaji</h2>

<ul class="nav nav-tabs mb-3" id="tabovi" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#rezervacije-tab">Sve rezervacije</button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#popunjenost-tab">Popunjenost termina</button>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="rezervacije-tab">
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Član</th>
                            <th>Termin</th>
                            <th>Trener</th>
                            <th>Datum termina</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rezervacije)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">Nema rezervacija.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($rezervacije as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['clan_ime'] . ' ' . $r['clan_prezime']) ?></td>
                            <td><?= htmlspecialchars($r['termin_naziv']) ?></td>
                            <td><?= htmlspecialchars($r['trener_ime'] . ' ' . $r['trener_prezime']) ?></td>
                            <td><?= date('d.m.Y', strtotime($r['termin_datum'])) ?></td>
                            <td>
                                <?php if ($r['status'] === 'potvrdjena'): ?>
                                    <span class="badge bg-success">Potvrđena</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Otkazana</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="popunjenost-tab">
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Termin</th>
                            <th>Datum</th>
                            <th>Popunjenost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($popunjenost as $p): ?>
                        <?php $procenat = $p['max_polaznika'] > 0 ? round(($p['broj_prijavljenih'] / $p['max_polaznika']) * 100) : 0; ?>
                        <tr>
                            <td><?= htmlspecialchars($p['naziv']) ?></td>
                            <td><?= date('d.m.Y', strtotime($p['datum'])) ?></td>
                            <td style="width: 300px;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 18px;">
                                        <div class="progress-bar <?= $procenat >= 100 ? 'bg-danger' : 'bg-primary' ?>" style="width: <?= min($procenat, 100) ?>%">
                                        </div>
                                    </div>
                                    <small class="text-nowrap"><?= $p['broj_prijavljenih'] ?>/<?= $p['max_polaznika'] ?></small>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
