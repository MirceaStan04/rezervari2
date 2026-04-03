<?php
require_once __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/layout.php';

$idLocatie = (int)($_GET['id'] ?? 0);
if ($idLocatie <= 0) {
    set_flash('warning', 'Locatie invalida.');
    header('Location: ' . url('locatii.php'));
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM locatii WHERE id_locatie = ?');
$stmt->execute([$idLocatie]);
$locatie = $stmt->fetch();

if (!$locatie) {
    set_flash('warning', 'Locatia nu exista in baza de date.');
    header('Location: ' . url('locatii.php'));
    exit;
}

$stmt = $pdo->prepare('SELECT id_eveniment, titlu, data_eveniment, ora_inceput, ora_sfarsit, pret FROM evenimente WHERE id_locatie = ? ORDER BY data_eveniment ASC, ora_inceput ASC LIMIT 6');
$stmt->execute([$idLocatie]);
$evenimenteLocatie = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT data_eveniment FROM evenimente WHERE id_locatie = ?');
$stmt->execute([$idLocatie]);
$zileOcupate = $stmt->fetchAll(PDO::FETCH_COLUMN);

$calendar = [];
$azi = new DateTimeImmutable('today');
for ($i = 0; $i < 21; $i++) {
    $calendar[] = $azi->modify("+{$i} days")->format('Y-m-d');
}

render_header($locatie['nume_locatie'] . ' - detalii locatie');
?>
<section class="split-panel mb-4">
    <div class="glass-panel overflow-hidden">
        <img class="location-card__image" style="height: 360px;" src="<?= e(loc_image($locatie['poza'] ?? '')) ?>" alt="<?= e($locatie['nume_locatie']) ?>">
    </div>
    <div class="glass-panel p-4 p-lg-5">
        <span class="card-tag mb-3"><i class="bi bi-house-heart"></i> locatie selectata</span>
        <h1 class="section-title mb-2"><?= e($locatie['nume_locatie']) ?></h1>
        <p class="section-subtitle mb-4"><?= e($locatie['adresa']) ?></p>
        <div class="d-grid gap-3 mb-4">
            <div class="detail-chip"><strong>Capacitate</strong><span><?= (int)$locatie['capacitate'] ?> persoane</span></div>
            <div class="detail-chip"><strong>Rol organizator</strong><span>Alege aceasta locatie cand trimiti o cerere noua de eveniment.</span></div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= e(url('propune_eveniment.php?locatie=' . $idLocatie)) ?>" class="btn btn-crimson rounded-pill">Propune eveniment aici</a>
            <a href="<?= e(url('locatii.php')) ?>" class="btn btn-soft rounded-pill">Inapoi la catalog</a>
        </div>
    </div>
</section>

<section class="mb-5">
    <div class="panel-header">
        <div>
            <h2 class="section-title mb-1">Disponibilitate pe urmatoarele 21 zile</h2>
            <p class="section-subtitle mb-0">Zilele ocupate sunt trase direct din tabelul evenimente deja aprobate.</p>
        </div>
    </div>
    <div class="calendar-grid">
        <?php foreach ($calendar as $zi): ?>
            <?php $ocupata = in_array($zi, $zileOcupate, true); ?>
            <div class="calendar-day <?= $ocupata ? 'is-busy' : 'is-free' ?>">
                <strong><?= e(format_date_ro($zi)) ?></strong>
                <div class="small text-secondary-emphasis mt-2"><?= $ocupata ? 'Are eveniment publicat' : 'Disponibila pentru propunere' ?></div>
                <?php if (!$ocupata): ?>
                    <a class="btn btn-soft btn-sm rounded-pill mt-3" href="<?= e(url('propune_eveniment.php?locatie=' . $idLocatie . '&data=' . $zi)) ?>">Alege ziua</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="glass-panel p-4 p-lg-5">
    <div class="panel-header">
        <div>
            <h2 class="section-title mb-1">Evenimente programate aici</h2>
            <p class="section-subtitle mb-0">Clientii se pot inscrie direct la ele.</p>
        </div>
    </div>
    <?php if ($evenimenteLocatie): ?>
        <div class="d-grid gap-3">
            <?php foreach ($evenimenteLocatie as $event): ?>
                <div class="highlight-card">
                    <div class="d-flex justify-content-between gap-3 flex-wrap align-items-center">
                        <div>
                            <strong><?= e($event['titlu']) ?></strong>
                            <div class="inline-meta mt-2">
                                <span><i class="bi bi-calendar2-week"></i> <?= e(format_date_ro($event['data_eveniment'])) ?></span>
                                <span><i class="bi bi-clock"></i> <?= e(substr($event['ora_inceput'], 0, 5)) ?> - <?= e(substr($event['ora_sfarsit'], 0, 5)) ?></span>
                                <span><i class="bi bi-cash-stack"></i> <?= e(format_money($event['pret'] ?? 0)) ?></span>
                            </div>
                        </div>
                        <a class="btn btn-crimson rounded-pill" href="<?= e(url('rezervare.php?eveniment=' . (int)$event['id_eveniment'])) ?>">Inscrie-te</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">Nu exista evenimente aprobate pentru aceasta locatie.</div>
    <?php endif; ?>
</section>
<?php render_footer(); ?>
