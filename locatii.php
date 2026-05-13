<?php
require_once __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/layout.php';

$locatii = $pdo->query('SELECT id_locatie, nume_locatie, adresa, capacitate, poza FROM locatii ORDER BY nume_locatie ASC')->fetchAll();

render_header('Locatii - EventFlow');
?>
<section class="mb-4">
    <span class="card-tag mb-3"><i class="bi bi-buildings"></i> catalog locatii</span>
    <h1 class="section-title mb-2">Locatii disponibile pentru evenimente</h1>
    <p class="section-subtitle">Am inlocuit listarea minimalista cu un catalog vizual, usor de vandut in fata unui profesor sau client.</p>
</section>

<?php if ($locatii): ?>
    <div class="location-grid">
        <?php foreach ($locatii as $loc): ?>
            <article class="card-surface location-card">
                <img class="location-card__image" src="<?= e(loc_image($loc['poza'] ?? '')) ?>" alt="<?= e($loc['nume_locatie']) ?>">
                <div class="location-card__body">
                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-2">
                        <span class="card-tag"><i class="bi bi-geo-alt"></i> Sibiu</span>
                        <span class="badge-soft neutral"><?= (int)$loc['capacitate'] ?> locuri</span>
                    </div>
                    <h2 class="h4 mb-2"><?= e($loc['nume_locatie']) ?></h2>
                    <p class="text-secondary-emphasis mb-3"><?= e($loc['adresa']) ?></p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?= e(url('locatie.php?id=' . (int)$loc['id_locatie'])) ?>" class="btn btn-crimson rounded-pill">Disponibilitate</a>
                        <a href="<?= e(url('propune_eveniment.php?locatie=' . (int)$loc['id_locatie'])) ?>" class="btn btn-soft rounded-pill">Propune eveniment</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="glass-panel empty-state">Nu exista locatii salvate momentan.</div>
<?php endif; ?>
<?php render_footer(); ?>
