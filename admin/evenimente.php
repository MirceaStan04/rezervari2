<?php
require_once __DIR__ . '/admin_check.php';
require __DIR__ . '/../includes/layout.php';

$evenimente = $pdo->query("SELECT e.id_eveniment, e.titlu, e.descriere, e.data_eveniment, e.ora_inceput, e.ora_sfarsit, e.pret,
                                  l.id_locatie, l.nume_locatie, l.poza,
                                  (SELECT COUNT(*) FROM rezervari r WHERE r.id_eveniment = e.id_eveniment) AS inscrieri_totale
                           FROM evenimente e
                           JOIN locatii l ON e.id_locatie = l.id_locatie
                           ORDER BY e.data_eveniment DESC, e.ora_inceput DESC")->fetchAll();

render_header('Admin - evenimente');
?>
<section class="mb-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
            <span class="card-tag mb-3"><i class="bi bi-calendar4-event"></i> management evenimente</span>
            <h1 class="section-title mb-2">Agenda completa</h1>
            <p class="section-subtitle">Evenimentele publicate in sistem. Propunerile noi se aproba din pagina de cereri.</p>
        </div>
        <a href="<?= e(url('admin/cereri_evenimente.php')) ?>" class="btn btn-soft rounded-pill"><i class="bi bi-inboxes"></i> Cereri in asteptare</a>
    </div>
</section>

<?php if ($evenimente): ?>
    <div class="event-grid">
        <?php foreach ($evenimente as $event): ?>
            <article class="card-surface event-card">
                <img class="event-card__image" src="<?= e(loc_image($event['poza'] ?? '')) ?>" alt="<?= e($event['titlu']) ?>">
                <div class="event-card__body">
                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-2">
                        <span class="card-tag">ID eveniment #<?= (int)$event['id_eveniment'] ?></span>
                        <span class="badge-soft neutral"><?= e(format_money($event['pret'] ?? 0)) ?></span>
                    </div>
                    <h2 class="h4 mb-2"><?= e($event['titlu']) ?></h2>
                    <div class="event-meta mb-3">
                        <span><i class="bi bi-calendar2-week"></i> <?= e(format_date_ro($event['data_eveniment'])) ?></span>
                        <span><i class="bi bi-clock"></i> <?= e(substr($event['ora_inceput'], 0, 5)) ?> - <?= e(substr($event['ora_sfarsit'], 0, 5)) ?></span>
                        <span><i class="bi bi-building"></i> <?= e($event['nume_locatie']) ?></span>
                    </div>
                    <p class="text-secondary-emphasis mb-3"><?= e(short_text((string)($event['descriere'] ?? 'Fara descriere.'), 140)) ?></p>
                    <div class="small text-secondary-emphasis mb-3">Inscrieri totale: <?= (int)$event['inscrieri_totale'] ?></div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?= e(url('eveniment.php?id=' . (int)$event['id_eveniment'])) ?>" class="btn btn-soft rounded-pill">Pagina publica</a>
                        <a href="<?= e(url('admin/editeaza_eveniment.php?id=' . (int)$event['id_eveniment'])) ?>" class="btn btn-soft rounded-pill"><i class="bi bi-pencil"></i> Editeaza</a>
                        <a href="<?= e(url('admin/sterge_eveniment.php?id=' . (int)$event['id_eveniment'])) ?>"
                           class="btn btn-soft rounded-pill text-danger"
                           onclick="return confirm('Stergi evenimentul <?= e(addslashes($event['titlu'])) ?>? Toate inscrierile asociate vor fi sterse.')">
                            <i class="bi bi-trash"></i> Sterge
                        </a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="glass-panel empty-state">Nu exista evenimente in sistem.</div>
<?php endif; ?>
<?php render_footer(); ?>
