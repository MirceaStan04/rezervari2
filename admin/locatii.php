<?php
require_once __DIR__ . '/admin_check.php';
require __DIR__ . '/../includes/layout.php';

$locatii = $pdo->query('SELECT * FROM locatii ORDER BY id_locatie DESC')->fetchAll();
render_header('Admin - locatii');
?>
<section class="mb-4">
    <span class="card-tag mb-3"><i class="bi bi-buildings"></i> management locatii</span>
    <h1 class="section-title mb-2">Locatii in sistem</h1>
    <p class="section-subtitle">Am scos tabela simpla si am trecut pe carduri administrative cu context vizual.</p>
</section>

<?php if ($locatii): ?>
    <div class="location-grid">
        <?php foreach ($locatii as $loc): ?>
            <article class="card-surface location-card">
                <img class="location-card__image" src="<?= e(loc_image($loc['poza'] ?? '')) ?>" alt="<?= e($loc['nume_locatie']) ?>">
                <div class="location-card__body">
                    <div class="d-flex justify-content-between gap-3 flex-wrap mb-2">
                        <span class="card-tag">ID #<?= (int)$loc['id_locatie'] ?></span>
                        <span class="badge-soft neutral"><?= (int)$loc['capacitate'] ?> locuri</span>
                    </div>
                    <h2 class="h4 mb-2"><?= e($loc['nume_locatie']) ?></h2>
                    <p class="text-secondary-emphasis mb-3"><?= e($loc['adresa']) ?></p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?= e(url('locatie.php?id=' . (int)$loc['id_locatie'])) ?>" class="btn btn-soft rounded-pill">Deschide pagina publica</a>
                        <a href="<?= e(url('propune_eveniment.php?locatie=' . (int)$loc['id_locatie'])) ?>" class="btn btn-crimson rounded-pill">Testeaza propunerea</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="glass-panel empty-state">Nu exista locatii in baza de date.</div>
<?php endif; ?>
<?php render_footer(); ?>
