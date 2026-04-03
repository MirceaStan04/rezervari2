<?php
require_once __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/layout.php';
require_client();

$idEveniment = (int)($_GET['eveniment'] ?? ($_POST['id_eveniment'] ?? 0));
if ($idEveniment <= 0) {
    set_flash('warning', 'Alege un eveniment valid pentru inscriere.');
    header('Location: ' . url('evenimente.php'));
    exit;
}

$stmt = $pdo->prepare("SELECT e.id_eveniment, e.titlu, e.descriere, e.data_eveniment, e.ora_inceput, e.ora_sfarsit, e.pret,
                              l.id_locatie, l.nume_locatie, l.adresa, l.capacitate, l.poza
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

$stmt = $pdo->prepare('SELECT COUNT(*) FROM rezervari WHERE id_eveniment = ? AND id_utilizator = ? AND status NOT IN (\'respinsa\', \'anulata\', \'rejected\', \'cancelled\')');
$stmt->execute([$idEveniment, (int)$_SESSION['user_id']]);
$already = (int)$stmt->fetchColumn() > 0;

render_header('Inscriere la eveniment - Crimson Reserve');
?>
<section class="split-panel">
    <div class="glass-panel p-4 p-lg-5">
        <span class="card-tag mb-3"><i class="bi bi-ticket-perforated"></i> inscriere eveniment</span>
        <h1 class="section-title mb-2"><?= e($event['titlu']) ?></h1>
        <p class="section-subtitle mb-4">Doar clientii se pot inscrie la evenimentele publicate. Organizatorii au un flux separat si pot trimite cereri pentru evenimente noi din meniul dedicat.</p>

        <?php if ($already): ?>
            <div class="notice-box mb-4">
                <strong>Esti deja inscris la acest eveniment</strong>
                <div class="text-secondary-emphasis small mt-2">Verifica statusul in pagina „Inscrierile mele”.</div>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= e(url('rezervare_submit.php')) ?>" class="row g-3 form-card">
            <input type="hidden" name="id_eveniment" value="<?= (int)$event['id_eveniment'] ?>">
            <div class="col-md-7">
                <label class="form-label">Numele evenimentului</label>
                <input type="text" class="form-control locked-input" value="<?= e($event['titlu']) ?>" disabled>
            </div>
            <div class="col-md-5">
                <label class="form-label">Numar locuri dorite</label>
                <input type="number" name="numar_locuri" class="form-control" required min="1" value="1">
            </div>
            <div class="col-md-4">
                <label class="form-label">Data</label>
                <input type="text" class="form-control locked-input" value="<?= e(format_date_ro($event['data_eveniment'])) ?>" disabled>
            </div>
            <div class="col-md-4">
                <label class="form-label">Ora inceput</label>
                <input type="text" class="form-control locked-input" value="<?= e(substr($event['ora_inceput'], 0, 5)) ?>" disabled>
            </div>
            <div class="col-md-4">
                <label class="form-label">Ora sfarsit</label>
                <input type="text" class="form-control locked-input" value="<?= e(substr($event['ora_sfarsit'], 0, 5)) ?>" disabled>
            </div>
            <div class="col-12 d-flex flex-wrap gap-2 mt-2">
                <button class="btn btn-crimson btn-lg rounded-pill px-4" type="submit" <?= $already ? 'disabled' : '' ?>>Trimite inscrierea</button>
                <a href="<?= e(url('rezervarile-mele.php')) ?>" class="btn btn-soft btn-lg rounded-pill px-4">Vezi inscrierile mele</a>
            </div>
        </form>
    </div>

    <aside class="glass-panel p-4 p-lg-5">
        <span class="card-tag mb-3"><i class="bi bi-building-check"></i> locatia evenimentului</span>
        <h2 class="section-title h3 mb-2"><?= e($event['nume_locatie']) ?></h2>
        <p class="section-subtitle mb-4"><?= e($event['adresa']) ?></p>
        <div class="d-grid gap-3 mb-4">
            <div class="detail-chip"><strong>Capacitate locatie</strong><span><?= (int)$event['capacitate'] ?> persoane</span></div>
            <div class="detail-chip"><strong>Tarif listat</strong><span><?= e(format_money($event['pret'] ?? 0)) ?></span></div>
        </div>
        <img class="location-card__image rounded-4" src="<?= e(loc_image($event['poza'] ?? '')) ?>" alt="<?= e($event['nume_locatie']) ?>">
    </aside>
</section>
<?php render_footer(); ?>
