<?php
require_once __DIR__ . '/admin_check.php';
require __DIR__ . '/../includes/layout.php';

$stats = [
    'locatii' => card_stat($pdo, 'locatii'),
    'evenimente' => card_stat($pdo, 'evenimente'),
    'rezervari' => card_stat($pdo, 'rezervari'),
    'utilizatori' => card_stat($pdo, 'utilizatori'),
];
$requestStats = safe_event_request_stats($pdo);

$rezervariRecente = $pdo->query("SELECT r.id_rezervare, r.status, r.numar_locuri,
                                        u.nume AS nume_utilizator,
                                        e.titlu, e.data_eveniment,
                                        l.nume_locatie
                                 FROM rezervari r
                                 JOIN utilizatori u ON r.id_utilizator = u.id_utilizator
                                 JOIN evenimente e ON r.id_eveniment = e.id_eveniment
                                 JOIN locatii l ON e.id_locatie = l.id_locatie
                                 ORDER BY r.id_rezervare DESC
                                 LIMIT 6")->fetchAll();

$statusGroups = ['in_asteptare' => 0, 'aprobata' => 0, 'respinsa' => 0];
foreach ($rezervariRecente as $rez) {
    $key = normalize_status($rez['status']);
    if (isset($statusGroups[$key])) {
        $statusGroups[$key]++;
    }
}

render_header('Admin dashboard - Crimson Reserve');
?>
<section class="mb-4">
    <span class="card-tag mb-3"><i class="bi bi-speedometer2"></i> zona de administrare</span>
    <h1 class="section-title mb-2">Panou administrator</h1>
    <p class="section-subtitle">Gestioneaza locatiile, evenimentele, utilizatorii, inscrierile si cererile de eveniment.</p>
</section>

<div class="dashboard-grid mb-5">
    <a class="card-surface text-decoration-none" href="<?= e(url('admin/locatii.php')) ?>"><strong class="d-block fs-3"><?= (int)$stats['locatii'] ?></strong><span class="text-secondary-emphasis">locatii in sistem</span></a>
    <a class="card-surface text-decoration-none" href="<?= e(url('admin/evenimente.php')) ?>"><strong class="d-block fs-3"><?= (int)$stats['evenimente'] ?></strong><span class="text-secondary-emphasis">evenimente publicate</span></a>
    <a class="card-surface text-decoration-none" href="<?= e(url('admin/rezervari.php')) ?>"><strong class="d-block fs-3"><?= (int)$stats['rezervari'] ?></strong><span class="text-secondary-emphasis">inscrieri totale</span></a>
    <a class="card-surface text-decoration-none" href="<?= e(url('admin/utilizatori.php')) ?>"><strong class="d-block fs-3"><?= (int)$stats['utilizatori'] ?></strong><span class="text-secondary-emphasis">utilizatori</span></a>
    <a class="card-surface text-decoration-none" href="<?= e(url('admin/cereri_evenimente.php')) ?>"><strong class="d-block fs-3"><?= (int)$requestStats['in_asteptare'] ?></strong><span class="text-secondary-emphasis">cereri organizatori in asteptare</span></a>
</div>

<section class="split-panel">
    <div class="glass-panel p-4 p-lg-5">
        <div class="panel-header">
            <div>
                <h2 class="section-title h3 mb-1">Inscrieri recente</h2>
                <p class="section-subtitle mb-0">Ce au rezervat clientii si participantii in ultima perioada.</p>
            </div>
        </div>
        <?php if ($rezervariRecente): ?>
            <div class="d-grid gap-3">
                <?php foreach ($rezervariRecente as $rez): ?>
                    <div class="highlight-card">
                        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                            <div>
                                <strong><?= e($rez['titlu']) ?></strong>
                                <div class="inline-meta mt-2">
                                    <span><i class="bi bi-person"></i> <?= e($rez['nume_utilizator']) ?></span>
                                    <span><i class="bi bi-building"></i> <?= e($rez['nume_locatie']) ?></span>
                                    <span><i class="bi bi-calendar2-week"></i> <?= e(format_date_ro($rez['data_eveniment'])) ?></span>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="<?= e(status_badge_class($rez['status'])) ?> d-inline-flex mb-2"><?= e(status_label($rez['status'])) ?></span>
                                <div class="small text-secondary-emphasis"><?= (int)$rez['numar_locuri'] ?> locuri</div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">Nu exista inscrieri pentru afisare.</div>
        <?php endif; ?>
    </div>

    <aside class="glass-panel p-4 p-lg-5">
        <div class="panel-header">
            <div>
                <h2 class="section-title h3 mb-1">Snapshot rapid</h2>
                <p class="section-subtitle mb-0">Atat pentru inscrieri, cat si pentru cereri de evenimente.</p>
            </div>
        </div>
        <div class="d-grid gap-3 mb-4">
            <div class="stat-pill"><strong><?= (int)$statusGroups['in_asteptare'] ?></strong><span>inscrieri recente in asteptare</span></div>
            <div class="stat-pill"><strong><?= (int)$statusGroups['aprobata'] ?></strong><span>inscrieri recente aprobate</span></div>
            <div class="stat-pill"><strong><?= (int)$requestStats['in_asteptare'] ?></strong><span>cereri organizatori in asteptare</span></div>
        </div>
        <div class="mt-4 d-grid gap-2">
            <a href="<?= e(url('admin/cereri_evenimente.php')) ?>" class="btn btn-crimson rounded-pill">Gestioneaza cererile de eveniment</a>
            <a href="<?= e(url('admin/rezervari.php')) ?>" class="btn btn-soft rounded-pill">Gestioneaza inscrierile</a>
        </div>
    </aside>
</section>
<?php render_footer(); ?>
