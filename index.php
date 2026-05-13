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
                           LIMIT 3")->fetchAll();

render_header('EventFlow – Gestiune Evenimente Sibiu');
?>

<!-- ─── Hero ─────────────────────────────────────────────────────────────── -->
<section class="hero-section">
    <span class="hero-kicker">
        <i class="bi bi-geo-alt-fill"></i> Sibiu &mdash; Evenimente &amp; Inscrieri
    </span>
    <h1 class="hero-title">
        Descopera evenimentele<br>
        din Sibiu &mdash; <span class="accent">rezerva-ti locul.</span>
    </h1>
    <p class="hero-subtitle">
        Descopera evenimentele din Sibiu, alege-ti locul si confirma inscrierea in cateva secunde.
    </p>
    <div class="hero-actions">
        <a href="<?= e(url('evenimente.php')) ?>" class="btn btn-crimson btn-lg rounded-pill px-4">
            <i class="bi bi-calendar2-event me-2"></i>Vezi evenimentele
        </a>
        <?php if (is_organizer()): ?>
            <a href="<?= e(url('propune_eveniment.php')) ?>" class="btn btn-soft btn-lg rounded-pill px-4">Propune eveniment</a>
        <?php elseif (!is_logged_in()): ?>
            <a href="<?= e(url('register.php')) ?>" class="btn btn-soft btn-lg rounded-pill px-4">Creeaza cont</a>
        <?php endif; ?>
    </div>
    <div class="hero-stats">
        <div class="hero-stat">
            <div class="hero-stat__num"><?= (int)$stats['locatii'] ?></div>
            <div class="hero-stat__lbl">locatii</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat__num"><?= (int)$stats['evenimente'] ?></div>
            <div class="hero-stat__lbl">evenimente</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat__num"><?= (int)$stats['inscrieri'] ?></div>
            <div class="hero-stat__lbl">inscrieri</div>
        </div>
    </div>
    <?php if (is_admin() && event_requests_ready($pdo) && $stats['cereri'] > 0): ?>
        <div class="notice-box mt-4" style="max-width:460px">
            <strong>Cereri in asteptare</strong>
            <div class="small mt-1" style="color:var(--muted)">
                <?= (int)$stats['cereri'] ?> cereri de la organizatori.
                <a href="<?= e(url('admin/cereri_evenimente.php')) ?>" style="color:var(--accent-text)">Gestioneaza &rarr;</a>
            </div>
        </div>
    <?php endif; ?>
</section>

<!-- ─── 2 roluri ──────────────────────────────────────────────────────────── -->
<section class="mb-5">
    <div class="roles-grid">
        <div class="role-card glass-panel">
            <div class="role-card__icon"><i class="bi bi-ticket-perforated"></i></div>
            <div class="role-card__body">
                <h3 class="role-card__title">Particip la evenimente</h3>
                <p class="role-card__text">Rasfoieste evenimentele disponibile, alege cate locuri vrei si urmareste statusul inscrierii din contul tau.</p>
                <a href="<?= e(url(!is_logged_in() ? 'register.php' : 'evenimente.php')) ?>" class="btn btn-soft rounded-pill btn-sm mt-2">
                    <?= !is_logged_in() ? 'Creeaza cont gratuit' : 'Vezi evenimentele' ?> <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
        <div class="role-card glass-panel">
            <div class="role-card__icon"><i class="bi bi-calendar2-plus"></i></div>
            <div class="role-card__body">
                <h3 class="role-card__title">Organizez evenimente</h3>
                <p class="role-card__text">Propune un eveniment cu locatie, interval orar si pret. Dupa aprobare apare public si participantii se pot inscrie direct.</p>
                <a href="<?= e(url(!is_logged_in() ? 'register.php?rol=organizator' : 'propune_eveniment.php')) ?>" class="btn btn-soft rounded-pill btn-sm mt-2">
                    <?= !is_logged_in() ? 'Inregistreaza-te ca organizator' : 'Propune eveniment' ?> <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
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
