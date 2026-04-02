<?php
require_once __DIR__ . '/includes/config.php';
require 'includes/layout.php';

$locatii = $pdo->query("SELECT * FROM locatii ORDER BY id_locatie ASC")->fetchAll();

render_header('Locații – Rezervări Evenimente Sibiu');
?>

<section class="mb-4">
    <div class="section-card p-4 p-lg-5">
        <span class="badge-soft mb-3"><i class="bi bi-building"></i> Locații disponibile</span>
        <h1 class="section-title mb-2">Alege spațiul potrivit pentru evenimentul tău</h1>
        <p class="section-subtitle mb-0">
            Fiecare locație include adresă, capacitate și acces rapid la calendarul de disponibilitate.
        </p>
    </div>
</section>

<?php if ($locatii): ?>
    <div class="row g-4">
        <?php foreach ($locatii as $loc): ?>
            <div class="col-md-6 col-xl-4">
                <article class="location-card h-100">
                    <?php if (!empty($loc['poza'])): ?>
                        <img src="<?= htmlspecialchars($loc['poza']) ?>" alt="<?= htmlspecialchars($loc['nume_locatie']) ?>" class="location-img">
                    <?php else: ?>
                        <div class="location-img d-flex justify-content-center align-items-center text-muted">
                            <i class="bi bi-image fs-1"></i>
                        </div>
                    <?php endif; ?>

                    <div class="p-4">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <h2 class="h4 fw-bold mb-1"><?= htmlspecialchars($loc['nume_locatie']) ?></h2>
                                <p class="text-muted mb-0"><i class="bi bi-geo-alt-fill me-1"></i><?= htmlspecialchars($loc['adresa']) ?></p>
                            </div>
                            <span class="badge-soft"><i class="bi bi-people-fill"></i><?= (int)$loc['capacitate'] ?></span>
                        </div>

                        <div class="soft-card p-3 mb-3">
                            <div class="small text-uppercase text-muted fw-semibold mb-1">Capacitate</div>
                            <div class="fw-bold fs-5"><?= (int)$loc['capacitate'] ?> persoane</div>
                        </div>

                        <a href="locatie.php?id=<?= (int)$loc['id_locatie'] ?>" class="btn btn-primary w-100">
                            <i class="bi bi-calendar3 me-2"></i>Vezi disponibilitatea
                        </a>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty-state">Nu există locații înregistrate în acest moment.</div>
<?php endif; ?>

<?php render_footer(); ?>
