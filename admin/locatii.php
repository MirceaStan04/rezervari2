<?php
require_once __DIR__ . '/../includes/config.php';
require '../includes/layout.php';
require 'admin_check.php';

$locatii = $pdo->query("SELECT * FROM locatii ORDER BY id_locatie")->fetchAll();

render_header('Gestionare Locații');
?>

<section class="mb-4">
    <div class="section-card p-4 p-lg-5">
        <h1 class="section-title mb-2">Gestionare locații</h1>
        <p class="section-subtitle mb-0">Listă completă cu locațiile disponibile în platformă.</p>
    </div>
</section>

<?php if ($locatii): ?>
    <div class="table-shell">
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nume</th>
                        <th>Adresă</th>
                        <th>Capacitate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($locatii as $loc): ?>
                        <tr>
                            <td><?= (int)$loc['id_locatie'] ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($loc['nume_locatie']) ?></td>
                            <td><?= htmlspecialchars($loc['adresa']) ?></td>
                            <td><?= (int)$loc['capacitate'] ?> persoane</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="empty-state">Nu există locații înregistrate.</div>
<?php endif; ?>

<?php render_footer(); ?>
