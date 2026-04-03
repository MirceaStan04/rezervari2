<?php
require_once __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/layout.php';

$tables = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name ASC")->fetchAll(PDO::FETCH_COLUMN);
render_header('Test conexiune PostgreSQL');
?>
<section class="glass-panel p-4 p-lg-5">
    <span class="card-tag mb-3"><i class="bi bi-database-check"></i> conexiune activa</span>
    <h1 class="section-title mb-2">Conexiunea la PostgreSQL functioneaza</h1>
    <p class="section-subtitle mb-4">Pagina asta verifica strict baza de date folosita de aplicatie, nu interfata pgAdmin4 in sine.</p>

    <div class="dashboard-grid mb-4">
        <div class="stat-pill"><strong><?= count($tables) ?></strong><span>tabele detectate in schema public</span></div>
        <div class="stat-pill"><strong><?= e($dbname ?? 'licenta') ?></strong><span>baza folosita de aplicatie</span></div>
        <div class="stat-pill"><strong><?= e($host ?? 'localhost') ?>:<?= e($port ?? '5432') ?></strong><span>server PostgreSQL</span></div>
    </div>

    <div class="glass-panel p-4">
        <h2 class="h5 mb-3">Tabele gasite</h2>
        <?php if ($tables): ?>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($tables as $table): ?>
                    <span class="badge-soft neutral"><?= e($table) ?></span>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">Nu au fost gasite tabele in schema public.</div>
        <?php endif; ?>
    </div>
</section>
<?php render_footer(); ?>
