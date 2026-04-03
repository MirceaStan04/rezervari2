<?php
require_once __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/layout.php';
require_client();

$stmt = $pdo->prepare("SELECT r.id_rezervare, r.numar_locuri, r.data_rezervare, r.status,
                              e.id_eveniment, e.titlu, e.descriere, e.data_eveniment, e.ora_inceput, e.ora_sfarsit, e.pret,
                              l.nume_locatie, l.adresa
                       FROM rezervari r
                       JOIN evenimente e ON r.id_eveniment = e.id_eveniment
                       JOIN locatii l ON e.id_locatie = l.id_locatie
                       WHERE r.id_utilizator = ?
                       ORDER BY r.id_rezervare DESC");
$stmt->execute([(int)$_SESSION['user_id']]);
$rezervari = $stmt->fetchAll();

render_header('Inscrierile mele - Crimson Reserve');
?>
<section class="mb-4">
    <span class="card-tag mb-3"><i class="bi bi-ticket-detailed"></i> contul meu</span>
    <h1 class="section-title mb-2">Inscrierile mele</h1>
    <p class="section-subtitle">Aici vezi evenimentele la care te-ai inscris ca participant, din rolul de client.</p>
</section>

<?php if ($rezervari): ?>
    <div class="event-grid">
        <?php foreach ($rezervari as $rez): ?>
            <article class="card-surface event-card">
                <div class="event-card__body">
                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                        <div>
                            <span class="card-tag mb-2"><i class="bi bi-building"></i> <?= e($rez['nume_locatie']) ?></span>
                            <h2 class="h4 mb-1"><?= e($rez['titlu']) ?></h2>
                            <div class="text-secondary-emphasis small"><?= e($rez['adresa']) ?></div>
                        </div>
                        <span class="<?= e(status_badge_class($rez['status'])) ?>"><?= e(status_label($rez['status'])) ?></span>
                    </div>
                    <div class="event-meta mb-3">
                        <span><i class="bi bi-calendar2-week"></i> <?= e(format_date_ro($rez['data_eveniment'])) ?></span>
                        <span><i class="bi bi-clock"></i> <?= e(substr($rez['ora_inceput'], 0, 5)) ?> - <?= e(substr($rez['ora_sfarsit'], 0, 5)) ?></span>
                        <span><i class="bi bi-people"></i> <?= (int)$rez['numar_locuri'] ?> locuri</span>
                    </div>
                    <p class="text-secondary-emphasis mb-3"><?= e(short_text((string)($rez['descriere'] ?? 'Fara descriere.'), 150)) ?></p>
                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                        <div class="small text-secondary-emphasis">Cerere inregistrata la: <?= e(format_date_ro($rez['data_rezervare'])) ?></div>
                        <div class="d-flex gap-2 flex-wrap">
                            <div class="badge-soft neutral"><?= e(format_money($rez['pret'] ?? 0)) ?></div>
                            <a href="<?= e(url('rezervare.php?eveniment=' . (int)$rez['id_eveniment'])) ?>" class="btn btn-soft btn-sm rounded-pill">Detalii</a>
                        </div>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="glass-panel empty-state">
        Nu ai inca inscrieri. <a href="<?= e(url('evenimente.php')) ?>">Alege un eveniment</a> si trimite prima rezervare.
    </div>
<?php endif; ?>
<?php render_footer(); ?>
