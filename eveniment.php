<?php
require_once __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/layout.php';

$idEveniment = (int)($_GET['id'] ?? 0);
if ($idEveniment <= 0) {
    set_flash('warning', 'Eveniment invalid.');
    header('Location: ' . url('evenimente.php'));
    exit;
}

$stmt = $pdo->prepare("SELECT e.*, l.id_locatie, l.nume_locatie, l.adresa, l.capacitate AS cap_locatie, l.poza
                       FROM evenimente e
                       JOIN locatii l ON e.id_locatie = l.id_locatie
                       WHERE e.id_eveniment = ?");
$stmt->execute([$idEveniment]);
$event = $stmt->fetch();

if (!$event) {
    set_flash('warning', 'Evenimentul nu exista.');
    header('Location: ' . url('evenimente.php'));
    exit;
}

$locStmt = $pdo->prepare("SELECT COALESCE(SUM(numar_locuri), 0) FROM rezervari
                          WHERE id_eveniment = ? AND status NOT IN ('respinsa','anulata','rejected','cancelled')");
$locStmt->execute([$idEveniment]);
$locuriOcupate  = (int)$locStmt->fetchColumn();
$capacitate     = (int)($event['capacitate'] ?? 0);
$locuriDisponibile = $capacitate > 0 ? max(0, $capacitate - $locuriOcupate) : null;
$soldOut        = $capacitate > 0 && $locuriDisponibile === 0;

$already = false;
if (is_client()) {
    $dupStmt = $pdo->prepare("SELECT COUNT(*) FROM rezervari WHERE id_eveniment = ? AND id_utilizator = ? AND status NOT IN ('respinsa','anulata','rejected','cancelled')");
    $dupStmt->execute([$idEveniment, (int)$_SESSION['user_id']]);
    $already = (int)$dupStmt->fetchColumn() > 0;
}

$azi    = date('Y-m-d');
$trecut = ($event['data_eveniment'] ?? '') < $azi;

render_header(e($event['titlu']) . ' - EventFlow');
?>
<nav class="mb-4">
    <a href="<?= e(url('evenimente.php')) ?>" class="btn btn-soft btn-sm rounded-pill"><i class="bi bi-arrow-left"></i> Inapoi la evenimente</a>
</nav>

<section class="split-panel mb-5">
    <div class="glass-panel p-4 p-lg-5">
        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
            <span class="card-tag"><i class="bi bi-calendar-event"></i> <?= e(format_date_ro($event['data_eveniment'])) ?></span>
            <?php if ($trecut): ?>
                <span class="badge-soft neutral">Eveniment finalizat</span>
            <?php elseif ($soldOut): ?>
                <span class="badge-soft danger">Sold out</span>
            <?php else: ?>
                <span class="badge-soft success">Locuri disponibile</span>
            <?php endif; ?>
        </div>

        <h1 class="section-title mb-3"><?= e($event['titlu']) ?></h1>

        <div class="event-meta mb-4">
            <span><i class="bi bi-clock"></i> <?= e(substr($event['ora_inceput'], 0, 5)) ?> – <?= e(substr($event['ora_sfarsit'], 0, 5)) ?></span>
            <span><i class="bi bi-building"></i> <?= e($event['nume_locatie']) ?></span>
            <span><i class="bi bi-pin-map"></i> <?= e($event['adresa']) ?></span>
        </div>

        <?php if (!empty($event['descriere'])): ?>
            <p class="mb-4" style="white-space: pre-line"><?= e($event['descriere']) ?></p>
        <?php endif; ?>

        <div class="d-grid gap-3 mb-4">
            <div class="detail-chip"><strong>Tarif</strong><span><?= e(format_money($event['pret'] ?? 0)) ?></span></div>
            <?php if ($capacitate > 0): ?>
                <div class="detail-chip">
                    <strong>Locuri disponibile</strong>
                    <span><?= $locuriDisponibile ?> / <?= $capacitate ?></span>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($already): ?>
            <div class="notice-box mb-4">
                <strong>Esti deja inscris la acest eveniment</strong>
                <div class="text-secondary-emphasis small mt-1">Verifica statusul inscrierii in zona „Inscrierile mele".</div>
            </div>
        <?php endif; ?>

        <?php if (!$trecut): ?>
            <div class="d-flex flex-wrap gap-2">
                <?php if (is_client() && !$already && !$soldOut): ?>
                    <a href="<?= e(url('rezervare.php?eveniment=' . (int)$event['id_eveniment'])) ?>" class="btn btn-crimson btn-lg rounded-pill px-4">
                        <i class="bi bi-ticket-perforated"></i> Inscrie-ma
                    </a>
                <?php elseif (!is_logged_in()): ?>
                    <a href="<?= e(url('login.php')) ?>" class="btn btn-crimson btn-lg rounded-pill px-4">Intra si te inscrie</a>
                <?php endif; ?>
                <a href="<?= e(url('locatie.php?id=' . (int)$event['id_locatie'])) ?>" class="btn btn-soft btn-lg rounded-pill px-4">
                    <i class="bi bi-buildings"></i> Detalii locatie
                </a>
            </div>
        <?php endif; ?>
    </div>

    <aside class="glass-panel overflow-hidden" style="max-height:600px">
        <img src="<?= e(loc_image($event['poza'] ?? '')) ?>"
             alt="<?= e($event['nume_locatie']) ?>"
             style="width:100%; height:100%; object-fit:cover; border-radius:var(--radius-xl)">
    </aside>
</section>
<?php render_footer(); ?>
