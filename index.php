<?php
require_once __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/layout.php';

$stats = [
    'locatii' => card_stat($pdo, 'locatii'),
    'evenimente' => card_stat($pdo, 'evenimente'),
    'inscrieri' => card_stat($pdo, 'rezervari'),
    'cereri' => pending_event_requests_count($pdo),
];

$locatii = $pdo->query('SELECT id_locatie, nume_locatie, adresa, capacitate, poza FROM locatii ORDER BY id_locatie DESC LIMIT 3')->fetchAll();
$evenimente = $pdo->query("SELECT e.id_eveniment, e.titlu, e.descriere, e.data_eveniment, e.ora_inceput, e.ora_sfarsit, e.pret, l.id_locatie, l.nume_locatie, l.adresa, l.poza
                          FROM evenimente e
                          JOIN locatii l ON e.id_locatie = l.id_locatie
                          WHERE e.data_eveniment >= CURRENT_DATE
                          ORDER BY e.data_eveniment ASC, e.ora_inceput ASC
                          LIMIT 4")->fetchAll();

render_header('EventFlow - evenimente si inscrieri');
?>
<section class="hero-grid mb-5">
    <div class="glass-panel hero-copy">
        <span class="hero-kicker"><i class="bi bi-stars"></i> evenimente & inscrieri in Sibiu</span>
        <h1 class="hero-title">Descopera evenimentele din Sibiu si rezerva-ti locul in cateva secunde.</h1>
        <p class="hero-subtitle">
            EventFlow conecteaza clientii cu evenimentele potrivite. Organizatorii propun, adminul valideaza, publicul se inscrie simplu si rapid.
        </p>
        <div class="d-flex flex-wrap gap-3 mt-4">
            <a href="<?= e(url('evenimente.php')) ?>" class="btn btn-crimson btn-lg rounded-pill px-4">Vezi evenimentele</a>
            <?php if (is_organizer()): ?>
                <a href="<?= e(url('propune_eveniment.php')) ?>" class="btn btn-soft btn-lg rounded-pill px-4">Propune eveniment</a>
            <?php elseif (!is_logged_in()): ?>
                <a href="<?= e(url('register.php')) ?>" class="btn btn-soft btn-lg rounded-pill px-4">Creeaza cont</a>
            <?php endif; ?>
        </div>
        <div class="metric-row">
            <div class="metric-tile">
                <strong><?= (int)$stats['locatii'] ?></strong>
                <span>locatii disponibile</span>
            </div>
            <div class="metric-tile">
                <strong><?= (int)$stats['evenimente'] ?></strong>
                <span>evenimente publicate</span>
            </div>
            <div class="metric-tile">
                <strong><?= (int)$stats['inscrieri'] ?></strong>
                <span>inscrieri totale</span>
            </div>
        </div>
        <?php if (is_admin() && event_requests_ready($pdo) && $stats['cereri'] > 0): ?>
            <div class="notice-box mt-4">
                <strong>Cereri in asteptare</strong>
                <div class="text-secondary-emphasis small mt-2"><?= (int)$stats['cereri'] ?> cereri de la organizatori asteapta aprobare. <a href="<?= e(url('admin/cereri_evenimente.php')) ?>">Gestioneaza</a></div>
            </div>
        <?php endif; ?>
    </div>

    <div class="glass-panel hero-side">
        <div class="highlight-card">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-ticket-detailed"></i>
                <div>
                    <strong>Flux client</strong>
                    <div class="text-secondary-emphasis">Clientul intra pe eveniment, cere 1 sau mai multe locuri si vede statusul in contul lui.</div>
                </div>
            </div>
        </div>
        <div class="highlight-card">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-send-check"></i>
                <div>
                    <strong>Flux organizator</strong>
                    <div class="text-secondary-emphasis">Organizatorul trimite o cerere de eveniment cu locatie, interval si pret propus.</div>
                </div>
            </div>
        </div>
        <div class="highlight-card">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-shield-check"></i>
                <div>
                    <strong>Flux admin</strong>
                    <div class="text-secondary-emphasis">Adminul aproba cererea si o transforma in eveniment public vizibil tuturor clientilor.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mb-5">
    <div class="panel-header">
        <div>
            <h2 class="section-title mb-1">Urmatoarele evenimente</h2>
            <p class="section-subtitle mb-0">Evenimente aprobate, disponibile pentru inscriere.</p>
        </div>
        <a href="<?= e(url('evenimente.php')) ?>" class="btn btn-soft rounded-pill">Vezi toate</a>
    </div>

    <?php if ($evenimente): ?>
        <div class="event-grid">
            <?php foreach ($evenimente as $ev): ?>
                <article class="card-surface event-card">
                    <img class="event-card__image" src="<?= e(loc_image($ev['poza'] ?? '')) ?>" alt="<?= e($ev['titlu']) ?>">
                    <div class="event-card__body">
                        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-2">
                            <span class="card-tag"><i class="bi bi-calendar-event"></i> <?= e(format_date_ro($ev['data_eveniment'])) ?></span>
                            <span class="badge-soft neutral"><?= e(format_money($ev['pret'] ?? 0)) ?></span>
                        </div>
                        <h3 class="h4 mb-2"><?= e($ev['titlu']) ?></h3>
                        <div class="inline-meta mb-2">
                            <span><i class="bi bi-clock"></i> <?= e(substr($ev['ora_inceput'], 0, 5)) ?> - <?= e(substr($ev['ora_sfarsit'], 0, 5)) ?></span>
                            <span><i class="bi bi-building"></i> <?= e($ev['nume_locatie']) ?></span>
                        </div>
                        <div class="text-secondary-emphasis small mb-3"><?= e(short_text((string)($ev['descriere'] ?? 'Eveniment disponibil pentru inscriere.'), 110)) ?></div>
                        <div class="d-flex gap-2 flex-wrap">
                            <?php if (is_client()): ?>
                                <a href="<?= e(url('rezervare.php?eveniment=' . (int)$ev['id_eveniment'])) ?>" class="btn btn-crimson rounded-pill">Inscrie-te</a>
                            <?php elseif (!is_logged_in()): ?>
                                <a href="<?= e(url('login.php')) ?>" class="btn btn-crimson rounded-pill">Intra ca sa te inscrii</a>
                            <?php elseif (is_organizer()): ?>
                                <a href="<?= e(url('propune_eveniment.php')) ?>" class="btn btn-soft rounded-pill">Propune eveniment</a>
                            <?php endif; ?>
                            <a href="<?= e(url('eveniment.php?id=' . (int)$ev['id_eveniment'])) ?>" class="btn btn-soft rounded-pill">Detalii</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="glass-panel empty-state">Nu exista inca evenimente aprobate in sistem.</div>
    <?php endif; ?>
</section>

<section class="split-panel mb-5">
    <div class="glass-panel p-4 p-lg-5">
        <span class="card-tag mb-3"><i class="bi bi-diagram-3"></i> flux nou</span>
        <h2 class="section-title mb-3">Cum functioneaza platforma</h2>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-item__dot"><i class="bi bi-person-badge"></i></div>
                <div class="timeline-item__body"><strong>1. Organizatorul trimite cererea</strong><div class="text-secondary-emphasis">Evenimentul nu apare public pana nu este aprobat.</div></div>
            </div>
            <div class="timeline-item">
                <div class="timeline-item__dot"><i class="bi bi-check2-square"></i></div>
                <div class="timeline-item__body"><strong>2. Adminul valideaza</strong><div class="text-secondary-emphasis">La aprobare se creeaza automat evenimentul public in tabelul evenimente.</div></div>
            </div>
            <div class="timeline-item">
                <div class="timeline-item__dot"><i class="bi bi-people"></i></div>
                <div class="timeline-item__body"><strong>3. Clientii se inscriu</strong><div class="text-secondary-emphasis">Inscrierile raman separate in rezervari si pot fi gestionate din admin.</div></div>
            </div>
        </div>
    </div>

    <div class="glass-panel p-4 p-lg-5">
        <span class="card-tag mb-3"><i class="bi bi-buildings"></i> locatii recomandate</span>
        <?php if ($locatii): ?>
            <div class="d-grid gap-3">
                <?php foreach ($locatii as $loc): ?>
                    <div class="highlight-card">
                        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                            <div>
                                <strong><?= e($loc['nume_locatie']) ?></strong>
                                <div class="text-secondary-emphasis small mt-2"><?= e($loc['adresa']) ?> • <?= (int)$loc['capacitate'] ?> persoane</div>
                            </div>
                            <a class="btn btn-soft rounded-pill" href="<?= e(url('propune_eveniment.php?locatie=' . (int)$loc['id_locatie'])) ?>">Propune aici</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">Nu exista locatii in baza de date.</div>
        <?php endif; ?>
    </div>
</section>
<?php render_footer(); ?>
