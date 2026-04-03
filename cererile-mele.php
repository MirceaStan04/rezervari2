<?php
require_once __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/layout.php';
require_organizer();

if (!event_requests_ready($pdo)) {
    render_header('Cererile mele - Crimson Reserve');
    ?>
    <div class="glass-panel empty-state">
        Lipseste tabela <strong>cereri_evenimente</strong>. Ruleaza scriptul <code>database/upgrade_roles_evenimente.sql</code> si reincearca.
    </div>
    <?php
    render_footer();
    return;
}

$stmt = $pdo->prepare("SELECT c.*, l.nume_locatie, l.adresa,
                              COALESCE(e.id_eveniment, 0) AS id_eveniment_creat
                       FROM cereri_evenimente c
                       JOIN locatii l ON c.id_locatie = l.id_locatie
                       LEFT JOIN evenimente e ON e.id_eveniment = c.id_eveniment_creat
                       WHERE c.id_organizator = ?
                       ORDER BY c.id_cerere DESC");
$stmt->execute([(int)$_SESSION['user_id']]);
$cereri = $stmt->fetchAll();

render_header('Cererile mele - Crimson Reserve');
?>
<section class="mb-4">
    <span class="card-tag mb-3"><i class="bi bi-send"></i> zona organizator</span>
    <h1 class="section-title mb-2">Cererile mele de eveniment</h1>
    <p class="section-subtitle">Vezi ce ai trimis spre aprobare si daca a fost deja transformat in eveniment public.</p>
</section>

<?php if ($cereri): ?>
    <div class="event-grid">
        <?php foreach ($cereri as $cerere): ?>
            <article class="card-surface event-card">
                <div class="event-card__body">
                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                        <div>
                            <span class="card-tag mb-2"><i class="bi bi-building"></i> <?= e($cerere['nume_locatie']) ?></span>
                            <h2 class="h4 mb-1"><?= e($cerere['titlu']) ?></h2>
                            <div class="text-secondary-emphasis small"><?= e($cerere['adresa']) ?></div>
                        </div>
                        <span class="<?= e(status_badge_class($cerere['status'])) ?>"><?= e(status_label($cerere['status'])) ?></span>
                    </div>
                    <div class="event-meta mb-3">
                        <span><i class="bi bi-calendar2-week"></i> <?= e(format_date_ro($cerere['data_eveniment'])) ?></span>
                        <span><i class="bi bi-clock"></i> <?= e(substr($cerere['ora_inceput'], 0, 5)) ?> - <?= e(substr($cerere['ora_sfarsit'], 0, 5)) ?></span>
                        <span><i class="bi bi-people"></i> <?= (int)$cerere['capacitate_estimata'] ?> persoane estimate</span>
                    </div>
                    <p class="text-secondary-emphasis mb-3"><?= e(short_text((string)($cerere['descriere'] ?? 'Fara descriere.'), 150)) ?></p>
                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                        <div class="badge-soft neutral"><?= e(format_money($cerere['pret'] ?? 0)) ?></div>
                        <?php if ((int)$cerere['id_eveniment_creat'] > 0): ?>
                            <a href="<?= e(url('rezervare.php?eveniment=' . (int)$cerere['id_eveniment_creat'])) ?>" class="btn btn-soft btn-sm rounded-pill">Vezi evenimentul publicat</a>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="glass-panel empty-state">Nu ai trimis inca nicio cerere. <a href="<?= e(url('propune_eveniment.php')) ?>">Trimite prima propunere</a>.</div>
<?php endif; ?>
<?php render_footer(); ?>
