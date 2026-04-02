<?php
require_once __DIR__ . '/../includes/config.php';
require '../includes/layout.php';
require 'admin_check.php';

$evenimente = $pdo->query("
    SELECT e.id_eveniment, e.titlu, e.data_eveniment, e.ora_inceput, e.ora_sfarsit, e.pret, l.nume_locatie
    FROM evenimente e
    JOIN locatii l ON e.id_locatie = l.id_locatie
    ORDER BY e.data_eveniment DESC, e.ora_inceput DESC
")->fetchAll();

render_header('Gestionare Evenimente');
?>

<section class="mb-4">
    <div class="section-card p-4 p-lg-5">
        <h1 class="section-title mb-2">Gestionare evenimente</h1>
        <p class="section-subtitle mb-0">Tabel centralizat cu evenimentele existente și locațiile asociate.</p>
    </div>
</section>

<?php if ($evenimente): ?>
    <div class="table-shell">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Eveniment</th>
                        <th>Data</th>
                        <th>Interval</th>
                        <th>Locație</th>
                        <th>Preț</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($evenimente as $ev): ?>
                        <tr>
                            <td><?= (int)$ev['id_eveniment'] ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($ev['titlu']) ?></td>
                            <td><?= htmlspecialchars($ev['data_eveniment']) ?></td>
                            <td><?= htmlspecialchars($ev['ora_inceput']) ?> - <?= htmlspecialchars($ev['ora_sfarsit']) ?></td>
                            <td><?= htmlspecialchars($ev['nume_locatie']) ?></td>
                            <td><?= htmlspecialchars($ev['pret']) ?> lei</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="empty-state">Nu există evenimente înregistrate.</div>
<?php endif; ?>

<?php render_footer(); ?>
