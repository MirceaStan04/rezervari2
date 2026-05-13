<?php
require_once __DIR__ . '/admin_check.php';
require __DIR__ . '/../includes/layout.php';

$locatii = $pdo->query('SELECT * FROM locatii ORDER BY id_locatie DESC')->fetchAll();
render_header('Admin - locatii');
?>
<section class="mb-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
            <span class="card-tag mb-3"><i class="bi bi-buildings"></i> management locatii</span>
            <h1 class="section-title mb-2">Locatii in sistem</h1>
            <p class="section-subtitle">Gestioneaza locatiile disponibile pentru evenimente. Poti adauga, edita sau sterge locatii.</p>
        </div>
        <a href="<?= e(url('admin/form_locatie.php')) ?>" class="btn btn-crimson rounded-pill"><i class="bi bi-plus-lg"></i> Adauga locatie</a>
    </div>
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
                        <a href="<?= e(url('locatie.php?id=' . (int)$loc['id_locatie'])) ?>" class="btn btn-soft rounded-pill">Pagina publica</a>
                        <a href="<?= e(url('admin/form_locatie.php?id=' . (int)$loc['id_locatie'])) ?>" class="btn btn-soft rounded-pill"><i class="bi bi-pencil"></i> Editeaza</a>
                        <a href="<?= e(url('admin/sterge_locatie.php?id=' . (int)$loc['id_locatie'])) ?>"
                           class="btn btn-soft rounded-pill text-danger"
                           onclick="return confirm('Stergi locatia <?= e(addslashes($loc['nume_locatie'])) ?>? Aceasta actiune nu poate fi anulata.')">
                            <i class="bi bi-trash"></i> Sterge
                        </a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="glass-panel empty-state">Nu exista locatii in baza de date. <a href="<?= e(url('admin/form_locatie.php')) ?>">Adauga prima locatie.</a></div>
<?php endif; ?>
<?php render_footer(); ?>
