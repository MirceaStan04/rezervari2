<?php
require_once __DIR__ . '/admin_check.php';
require __DIR__ . '/../includes/layout.php';

$rezervari = $pdo->query("SELECT r.id_rezervare, r.status, r.numar_locuri, r.data_rezervare,
                                 u.nume AS nume_utilizator, u.email,
                                 e.titlu, e.data_eveniment, e.ora_inceput, e.ora_sfarsit,
                                 l.nume_locatie
                          FROM rezervari r
                          JOIN utilizatori u ON r.id_utilizator = u.id_utilizator
                          JOIN evenimente e ON r.id_eveniment = e.id_eveniment
                          JOIN locatii l ON e.id_locatie = l.id_locatie
                          ORDER BY r.id_rezervare DESC")->fetchAll();

$coloane = [
    'in_asteptare' => [],
    'aprobata' => [],
    'respinsa' => [],
];
foreach ($rezervari as $rez) {
    $key = normalize_status($rez['status']);
    $coloane[$key] = $coloane[$key] ?? [];
    $coloane[$key][] = $rez;
}

render_header('Admin - inscrieri');
?>
<section class="mb-4">
    <span class="card-tag mb-3"><i class="bi bi-clipboard-check"></i> management inscrieri</span>
    <h1 class="section-title mb-2">Inscrieri la evenimente</h1>
    <p class="section-subtitle">Clientii rezerva locuri la evenimente deja publicate. Adminul aproba sau respinge inscrierile exact ca intr-un flux real.</p>
</section>

<div class="kanban-grid">
    <?php foreach (['in_asteptare' => 'In asteptare', 'aprobata' => 'Aprobate', 'respinsa' => 'Respinse'] as $key => $title): ?>
        <section class="kanban-column">
            <div class="panel-header mb-3">
                <div>
                    <h2 class="h5 mb-1"><?= e($title) ?></h2>
                    <div class="text-secondary-emphasis small"><?= count($coloane[$key] ?? []) ?> inscrieri</div>
                </div>
            </div>
            <?php if (!empty($coloane[$key])): ?>
                <?php foreach ($coloane[$key] as $rez): ?>
                    <article class="kanban-card">
                        <div class="d-flex justify-content-between gap-3 flex-wrap mb-2">
                            <strong>#<?= (int)$rez['id_rezervare'] ?> - <?= e($rez['titlu']) ?></strong>
                            <span class="<?= e(status_badge_class($rez['status'])) ?>"><?= e(status_label($rez['status'])) ?></span>
                        </div>
                        <div class="small text-secondary-emphasis mb-3">
                            <?= e($rez['nume_utilizator']) ?> • <?= e($rez['email']) ?><br>
                            <?= e($rez['nume_locatie']) ?> • <?= e(format_date_ro($rez['data_eveniment'])) ?> • <?= e(substr($rez['ora_inceput'], 0, 5)) ?> - <?= e(substr($rez['ora_sfarsit'], 0, 5)) ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                            <span class="badge-soft neutral"><?= (int)$rez['numar_locuri'] ?> locuri</span>
                            <?php if ($key === 'in_asteptare'): ?>
                                <div class="d-flex gap-2">
                                    <a class="btn btn-crimson btn-sm rounded-pill" href="<?= e(url('admin/update_rezervare_status.php?id=' . (int)$rez['id_rezervare'] . '&action=approve')) ?>">Aproba</a>
                                    <a class="btn btn-soft btn-sm rounded-pill" href="<?= e(url('admin/update_rezervare_status.php?id=' . (int)$rez['id_rezervare'] . '&action=reject')) ?>">Respinge</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state py-4">Nimic aici momentan.</div>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
</div>
<?php render_footer(); ?>
