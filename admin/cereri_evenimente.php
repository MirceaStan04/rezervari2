<?php
require_once __DIR__ . '/admin_check.php';
require __DIR__ . '/../includes/layout.php';

render_header('Admin - cereri evenimente');

if (!event_requests_ready($pdo)) {
    ?>
    <section class="mb-4">
        <span class="card-tag mb-3"><i class="bi bi-inboxes"></i> cereri organizatori</span>
        <h1 class="section-title mb-2">Functionalitate indisponibila</h1>
        <p class="section-subtitle">Aceasta sectiune nu este disponibila momentan. Contactati administratorul de sistem.</p>
    </section>
    <?php
    render_footer();
    return;
}

$requests = $pdo->query("SELECT c.*, u.nume AS nume_organizator, u.email,
                                l.nume_locatie, l.adresa
                         FROM cereri_evenimente c
                         JOIN utilizatori u ON c.id_organizator = u.id_utilizator
                         JOIN locatii l ON c.id_locatie = l.id_locatie
                         ORDER BY c.id_cerere DESC")->fetchAll();

$columns = ['in_asteptare' => [], 'aprobata' => [], 'respinsa' => []];
foreach ($requests as $req) {
    $key = normalize_status($req['status']);
    $columns[$key] = $columns[$key] ?? [];
    $columns[$key][] = $req;
}
?>
<section class="mb-4">
    <span class="card-tag mb-3"><i class="bi bi-inboxes"></i> cereri organizatori</span>
    <h1 class="section-title mb-2">Cereri de creare eveniment</h1>
    <p class="section-subtitle">Revizuieste propunerile organizatorilor si decide ce apare public pe platforma.</p>
</section>

<div class="kanban-grid">
    <?php foreach (['in_asteptare' => 'In asteptare', 'aprobata' => 'Aprobate', 'respinsa' => 'Respinse'] as $key => $title): ?>
        <section class="kanban-column">
            <div class="panel-header mb-3">
                <div>
                    <h2 class="h5 mb-1"><?= e($title) ?></h2>
                    <div class="text-secondary-emphasis small"><?= count($columns[$key] ?? []) ?> cereri</div>
                </div>
            </div>
            <?php if (!empty($columns[$key])): ?>
                <?php foreach ($columns[$key] as $req): ?>
                    <article class="kanban-card">
                        <div class="d-flex justify-content-between gap-3 flex-wrap mb-2">
                            <strong>#<?= (int)$req['id_cerere'] ?> - <?= e($req['titlu']) ?></strong>
                            <span class="<?= e(status_badge_class($req['status'])) ?>"><?= e(status_label($req['status'])) ?></span>
                        </div>
                        <div class="small text-secondary-emphasis mb-3">
                            <?= e($req['nume_organizator']) ?> • <?= e($req['email']) ?><br>
                            <?= e($req['nume_locatie']) ?> • <?= e(format_date_ro($req['data_eveniment'])) ?> • <?= e(substr($req['ora_inceput'], 0, 5)) ?> - <?= e(substr($req['ora_sfarsit'], 0, 5)) ?>
                        </div>
                        <div class="small text-secondary-emphasis mb-3"><?= e(short_text((string)($req['descriere'] ?? ''), 120)) ?></div>
                        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                            <span class="badge-soft neutral"><?= (int)$req['capacitate_estimata'] ?> pers. • <?= e(format_money($req['pret'] ?? 0)) ?></span>
                            <?php if ($key === 'in_asteptare'): ?>
                                <div class="d-flex gap-2">
                                    <a class="btn btn-crimson btn-sm rounded-pill" href="<?= e(url('admin/proceseaza_cerere_eveniment.php?id=' . (int)$req['id_cerere'] . '&action=approve')) ?>">Aproba</a>
                                    <a class="btn btn-soft btn-sm rounded-pill" href="<?= e(url('admin/proceseaza_cerere_eveniment.php?id=' . (int)$req['id_cerere'] . '&action=reject')) ?>">Respinge</a>
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
