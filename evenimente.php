<?php
require_once __DIR__ . '/includes/config.php';
require 'includes/layout.php';

$evenimente = $pdo->query("
    SELECT e.*, l.nume_locatie
    FROM evenimente e
    JOIN locatii l ON e.id_locatie = l.id_locatie
    ORDER BY e.data_eveniment DESC, e.ora_inceput DESC
")->fetchAll();

render_header('Evenimente – Rezervări Evenimente Sibiu');
?>

<section class="mb-4">
    <div class="section-card p-4 p-lg-5">
        <span class="badge-soft mb-3"><i class="bi bi-calendar-event"></i> Evenimente programate</span>
        <h1 class="section-title mb-2">Calendarul evenimentelor înregistrate</h1>
        <p class="section-subtitle mb-0">Vezi rapid titlul, data, intervalul orar, locația și prețul fiecărui eveniment.</p>
    </div>
</section>

<?php if ($evenimente): ?>
    <div class="table-shell">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Titlu</th>
                        <th>Data</th>
                        <th>Interval orar</th>
                        <th>Locație</th>
                        <th>Preț</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($evenimente as $e): ?>
                        <tr>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($e['titlu']) ?></div>
                                <?php if (!empty($e['descriere'])): ?>
                                    <div class="text-muted small mt-1"><?= htmlspecialchars(mb_strimwidth($e['descriere'], 0, 90, '...')) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge text-bg-primary rounded-pill px-3 py-2"><?= htmlspecialchars($e['data_eveniment']) ?></span></td>
                            <td><?= htmlspecialchars($e['ora_inceput'] . ' - ' . $e['ora_sfarsit']) ?></td>
                            <td><?= htmlspecialchars($e['nume_locatie']) ?></td>
                            <td><?= htmlspecialchars($e['pret']) ?> lei</td>
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
