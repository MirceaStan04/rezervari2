<?php
require_once __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/layout.php';

$stats = [
    'locatii'  => card_stat($pdo, 'locatii'),
    'evenimente' => card_stat($pdo, 'evenimente'),
    'inscrieri'  => card_stat($pdo, 'rezervari'),
    'cereri'     => pending_event_requests_count($pdo),
];

$locatii   = $pdo->query('SELECT id_locatie, nume_locatie, adresa, capacitate, poza FROM locatii ORDER BY id_locatie DESC LIMIT 3')->fetchAll();
$evenimente = $pdo->query("SELECT e.id_eveniment, e.titlu, e.descriere, e.data_eveniment,
                                  e.ora_inceput, e.ora_sfarsit, e.pret,
                                  l.id_locatie, l.nume_locatie, l.adresa, l.poza
                           FROM evenimente e
                           JOIN locatii l ON e.id_locatie = l.id_locatie
                           WHERE e.data_eveniment >= CURRENT_DATE
                           ORDER BY e.data_eveniment ASC, e.ora_inceput ASC
                           LIMIT 4")->fetchAll();

render_header('EventFlow – Gestiune Evenimente Sibiu');
?>

<!-- ─── Hero ─────────────────────────────────────────────────────────────── -->
<section class="hero-section">
    <span class="hero-kicker mb-3 d-inline-flex">
        <i class="bi bi-geo-alt-fill"></i> Sibiu &bull; evenimente &amp; inscrieri
    </span>
    <h1 class="hero-title" style="max-width:820px">
        Descopera evenimentele din Sibiu<br>si rezerva-ti locul in secunde.
    </h1>
    <p class="hero-subtitle">
        Organizatorii propun, adminul valideaza, publicul se inscrie.
        Un singur flux, trei roluri clare.
    </p>
    <div class="d-flex flex-wrap gap-3 mt-4">
        <a href="<?= e(url('evenimente.php')) ?>" class="btn btn-crimson btn-lg rounded-pill px-4">
            <i class="bi bi-calendar2-event me-2"></i>Vezi evenimentele
        </a>
        <?php if (is_organizer()): ?>
            <a href="<?= e(url('propune_eveniment.php')) ?>" class="btn btn-soft btn-lg rounded-pill px-4">Propune eveniment</a>
        <?php elseif (!is_logged_in()): ?>
            <a href="<?= e(url('register.php')) ?>" class="btn btn-soft btn-lg rounded-pill px-4">Creeaza cont gratuit</a>
        <?php endif; ?>
    </div>
    <div class="hero-stats-row">
        <div class="hero-stat">
            <i class="bi bi-buildings" style="font-size:1.3rem;color:var(--accent-text);flex-shrink:0"></i>
            <div><strong><?= (int)$stats['locatii'] ?></strong><span>locatii</span></div>
        </div>
        <div class="hero-stat">
            <i class="bi bi-calendar2-check" style="font-size:1.3rem;color:var(--accent-text);flex-shrink:0"></i>
            <div><strong><?= (int)$stats['evenimente'] ?></strong><span>evenimente</span></div>
        </div>
        <div class="hero-stat">
            <i class="bi bi-ticket-perforated" style="font-size:1.3rem;color:var(--accent-text);flex-shrink:0"></i>
            <div><strong><?= (int)$stats['inscrieri'] ?></strong><span>inscrieri</span></div>
        </div>
    </div>
    <?php if (is_admin() && event_requests_ready($pdo) && $stats['cereri'] > 0): ?>
        <div class="notice-box mt-4" style="max-width:480px">
            <strong>Cereri in asteptare</strong>
            <div class="text-secondary-emphasis small mt-1">
                <?= (int)$stats['cereri'] ?> cereri de la organizatori.
                <a href="<?= e(url('admin/cereri_evenimente.php')) ?>">Gestioneaza &rarr;</a>
            </div>
        </div>
    <?php endif; ?>
</section>

<!-- ─── 3 roluri ──────────────────────────────────────────────────────────── -->
<section class="mb-5 pt-2">
    <div class="panel-header mb-4">
        <div>
            <h2 class="section-title mb-1">Trei roluri. Un flux simplu.</h2>
            <p class="section-subtitle mb-0">Fiecare utilizator are zona lui clara in platforma.</p>
        </div>
    </div>
    <div class="features-grid">
        <div class="feature-card glass-panel">
            <div class="feature-icon"><i class="bi bi-people-fill"></i></div>
            <h3>Client</h3>
            <p class="text-secondary-emphasis mb-0">Browsezi evenimentele aprobate, alegi cate locuri vrei si urmaresti statusul inscrierii din cont.</p>
        </div>
        <div class="feature-card glass-panel">
            <div class="feature-icon"><i class="bi bi-send-check-fill"></i></div>
            <h3>Organizator</h3>
            <p class="text-secondary-emphasis mb-0">Trimiti o cerere de eveniment cu locatie, interval orar si pret. Cererea intra la aprobare adminului.</p>
        </div>
        <div class="feature-card glass-panel">
            <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
            <h3>Administrator</h3>
            <p class="text-secondary-emphasis mb-0">Revizuiesti cererile organizatorilor, aprobi sau respingi, si gestionezi toate inscrierile din dashboard.</p>
        </div>
    </div>
</section>

<!-- ─── Evenimente urmatoare ──────────────────────────────────────────────── -->
<section class="mb-5">
    <div class="panel-header">
        <div>
            <h2 class="section-title mb-1">Urmatoarele evenimente</h2>
            <p class="section-subtitle mb-0">Evenimente aprobate, disponibile pentru inscriere.</p>
        </div>
        <a href="<?= e(url('evenimente.php')) ?>" class="btn btn-soft rounded-pill">
            Vezi toate <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <?php if ($evenimente): ?>
        <div class="event-grid">
            <?php foreach ($evenimente as $ev): ?>
                <article class="card-surface event-card">
                    <img class="event-card__image"
                         src="<?= e(loc_image($ev['poza'] ?? '')) ?>"
                         alt="<?= e($ev['titlu']) ?>">
                    <div class="event-card__body">
                        <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap mb-2">
                            <span class="card-tag">
                                <i class="bi bi-calendar-event"></i>
                                <?= e(format_date_ro($ev['data_eveniment'])) ?>
                            </span>
                            <span class="badge-soft neutral"><?= e(format_money($ev['pret'] ?? 0)) ?></span>
                        </div>
                        <h3 class="h5 fw-bold mb-2"><?= e($ev['titlu']) ?></h3>
                        <div class="inline-meta mb-2">
                            <span><i class="bi bi-clock"></i> <?= e(substr($ev['ora_inceput'], 0, 5)) ?>–<?= e(substr($ev['ora_sfarsit'], 0, 5)) ?></span>
                            <span><i class="bi bi-building"></i> <?= e($ev['nume_locatie']) ?></span>
                        </div>
                        <p class="text-secondary-emphasis small mb-3">
                            <?= e(short_text((string)($ev['descriere'] ?? 'Eveniment disponibil pentru inscriere.'), 100)) ?>
                        </p>
                        <div class="d-flex gap-2 flex-wrap">
                            <?php if (is_client()): ?>
                                <a href="<?= e(url('rezervare.php?eveniment=' . (int)$ev['id_eveniment'])) ?>" class="btn btn-crimson btn-sm rounded-pill">Inscrie-te</a>
                            <?php elseif (!is_logged_in()): ?>
                                <a href="<?= e(url('login.php')) ?>" class="btn btn-crimson btn-sm rounded-pill">Intra si inscrie-te</a>
                            <?php endif; ?>
                            <a href="<?= e(url('eveniment.php?id=' . (int)$ev['id_eveniment'])) ?>" class="btn btn-soft btn-sm rounded-pill">Detalii</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="glass-panel empty-state">Nu exista inca evenimente aprobate in sistem.</div>
    <?php endif; ?>
</section>

<!-- ─── Cum functioneaza + Locatii ────────────────────────────────────────── -->
<section class="split-panel mb-5">
    <div class="glass-panel p-4 p-lg-5">
        <span class="card-tag mb-3"><i class="bi bi-diagram-3"></i> workflow</span>
        <h2 class="section-title h4 mb-4">De la propunere la inscriere</h2>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-item__dot"><i class="bi bi-person-badge"></i></div>
                <div class="timeline-item__body">
                    <strong>1. Organizatorul trimite cererea</strong>
                    <div class="text-secondary-emphasis small mt-1">Evenimentul nu apare public pana nu este aprobat.</div>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-item__dot"><i class="bi bi-check2-square"></i></div>
                <div class="timeline-item__body">
                    <strong>2. Adminul valideaza</strong>
                    <div class="text-secondary-emphasis small mt-1">La aprobare se creeaza automat evenimentul public.</div>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-item__dot"><i class="bi bi-ticket-perforated"></i></div>
                <div class="timeline-item__body">
                    <strong>3. Clientii se inscriu</strong>
                    <div class="text-secondary-emphasis small mt-1">Inscrierile sunt gestionate separat in dashboard-ul de admin.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="glass-panel p-4 p-lg-5">
        <span class="card-tag mb-3"><i class="bi bi-buildings"></i> locatii</span>
        <h2 class="section-title h4 mb-4">Locatii disponibile</h2>
        <?php if ($locatii): ?>
            <div class="d-grid gap-3">
                <?php foreach ($locatii as $loc): ?>
                    <div class="highlight-card">
                        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                            <div>
                                <strong><?= e($loc['nume_locatie']) ?></strong>
                                <div class="text-secondary-emphasis small mt-1">
                                    <?= e($loc['adresa']) ?> &bull; <?= (int)$loc['capacitate'] ?> locuri
                                </div>
                            </div>
                            <a href="<?= e(url('locatie.php?id=' . (int)$loc['id_locatie'])) ?>" class="btn btn-soft btn-sm rounded-pill">
                                Vezi &rarr;
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-3">
                <a href="<?= e(url('locatii.php')) ?>" class="btn btn-soft rounded-pill w-100">
                    <i class="bi bi-grid me-1"></i> Toate locatiile
                </a>
            </div>
        <?php else: ?>
            <div class="empty-state">Nu exista locatii in baza de date.</div>
        <?php endif; ?>
    </div>
</section>

<?php render_footer(); ?>
