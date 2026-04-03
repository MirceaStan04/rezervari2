<?php
require_once __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/layout.php';

$evenimente = $pdo->query("SELECT e.id_eveniment, e.titlu, e.descriere, e.data_eveniment, e.ora_inceput, e.ora_sfarsit, e.pret,
                                  l.id_locatie, l.nume_locatie, l.adresa, l.poza
                           FROM evenimente e
                           JOIN locatii l ON e.id_locatie = l.id_locatie
                           ORDER BY e.data_eveniment ASC, e.ora_inceput ASC")->fetchAll();

$azi = date('Y-m-d');
$viitoare = [];
$trecute = [];
foreach ($evenimente as $event) {
    if (($event['data_eveniment'] ?? '') >= $azi) {
        $viitoare[] = $event;
    } else {
        $trecute[] = $event;
    }
}

render_header('Evenimente - Crimson Reserve');
?>
<section class="mb-4">
    <span class="card-tag mb-3"><i class="bi bi-mic"></i> agenda publica</span>
    <h1 class="section-title mb-2">Evenimente active in sistem</h1>
    <p class="section-subtitle">Aici intra doar evenimentele deja aprobate de admin. Doar clientii se pot inscrie, iar organizatorii propun alte evenimente din formularul dedicat.</p>
</section>

<section class="mb-5">
    <div class="panel-header">
        <div>
            <h2 class="section-title mb-1">Urmatoarele evenimente</h2>
            <p class="section-subtitle mb-0">Carduri publice cu call to action clar pentru inscriere.</p>
        </div>
        <?php if (is_organizer()): ?>
            <a href="<?= e(url('propune_eveniment.php')) ?>" class="btn btn-soft rounded-pill">Propune un eveniment</a>
        <?php elseif (!is_logged_in()): ?>
            <a href="<?= e(url('register.php')) ?>" class="btn btn-soft rounded-pill">Creeaza cont</a>
        <?php endif; ?>
    </div>
    <?php if ($viitoare): ?>
        <div class="event-grid">
            <?php foreach ($viitoare as $event): ?>
                <article class="card-surface event-card">
                    <img class="event-card__image" src="<?= e(loc_image($event['poza'] ?? '')) ?>" alt="<?= e($event['titlu']) ?>">
                    <div class="event-card__body">
                        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-2">
                            <span class="card-tag"><i class="bi bi-calendar-event"></i> <?= e(format_date_ro($event['data_eveniment'])) ?></span>
                            <span class="badge-soft neutral"><?= e(format_money($event['pret'] ?? 0)) ?></span>
                        </div>
                        <h2 class="h4 mb-2"><?= e($event['titlu']) ?></h2>
                        <p class="text-secondary-emphasis mb-3"><?= e(short_text((string)($event['descriere'] ?? 'Fara descriere.'), 140)) ?></p>
                        <div class="event-meta mb-4">
                            <span><i class="bi bi-clock"></i> <?= e(substr($event['ora_inceput'], 0, 5)) ?> - <?= e(substr($event['ora_sfarsit'], 0, 5)) ?></span>
                            <span><i class="bi bi-building"></i> <?= e($event['nume_locatie']) ?></span>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <?php if (is_client()): ?>
                                <a href="<?= e(url('rezervare.php?eveniment=' . (int)$event['id_eveniment'])) ?>" class="btn btn-crimson rounded-pill">Inscrie-ma</a>
                            <?php elseif (is_organizer()): ?>
                                <a href="<?= e(url('propune_eveniment.php')) ?>" class="btn btn-soft rounded-pill">Propune eveniment</a>
                            <?php elseif (!is_logged_in()): ?>
                                <a href="<?= e(url('login.php')) ?>" class="btn btn-crimson rounded-pill">Intra ca sa te inscrii</a>
                            <?php endif; ?>
                            <a href="<?= e(url('locatie.php?id=' . (int)$event['id_locatie'])) ?>" class="btn btn-soft rounded-pill">Vezi locatia</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="glass-panel empty-state">Nu exista evenimente viitoare.</div>
    <?php endif; ?>
</section>

<section class="glass-panel p-4 p-lg-5">
    <div class="panel-header">
        <div>
            <h2 class="section-title mb-1">Arhiva recenta</h2>
            <p class="section-subtitle mb-0">Lista compacta pentru evenimentele deja trecute.</p>
        </div>
    </div>
    <?php if ($trecute): ?>
        <div class="d-grid gap-3">
            <?php foreach (array_slice($trecute, 0, 6) as $event): ?>
                <div class="highlight-card">
                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                        <div>
                            <strong><?= e($event['titlu']) ?></strong>
                            <div class="inline-meta mt-2">
                                <span><i class="bi bi-calendar2-week"></i> <?= e(format_date_ro($event['data_eveniment'])) ?></span>
                                <span><i class="bi bi-pin-map"></i> <?= e($event['nume_locatie']) ?></span>
                            </div>
                        </div>
                        <span class="badge-soft neutral">Finalizat</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">Nu exista evenimente trecute afisabile.</div>
    <?php endif; ?>
</section>
<?php render_footer(); ?>
