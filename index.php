<?php
require_once __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/layout.php';

$stats = [
    'locatii' => card_stat($pdo, 'locatii'),
    'evenimente' => card_stat($pdo, 'evenimente'),
    'rezervari' => card_stat($pdo, 'rezervari'),
];

$locatii = $pdo->query('SELECT id_locatie, nume_locatie, adresa, capacitate, poza FROM locatii ORDER BY id_locatie DESC LIMIT 3')->fetchAll();
$evenimente = $pdo->query("SELECT e.id_eveniment, e.titlu, e.descriere, e.data_eveniment, e.ora_inceput, e.ora_sfarsit, e.pret, l.id_locatie, l.nume_locatie, l.adresa, l.poza
                          FROM evenimente e
                          JOIN locatii l ON e.id_locatie = l.id_locatie
                          ORDER BY e.data_eveniment ASC, e.ora_inceput ASC
                          LIMIT 4")->fetchAll();

render_header('Crimson Reserve - rezervari evenimente');
?>
<section class="hero-grid mb-5">
    <div class="glass-panel hero-copy">
        <span class="hero-kicker"><i class="bi bi-stars"></i> varianta premium pentru proiectul tau</span>
        <h1 class="hero-title">Rezervari de evenimente care arata ca un produs real, nu ca un simplu laborator.</h1>
        <p class="hero-subtitle">
            Cauta locatii, verifica rapid disponibilitatea, trimite cereri de rezervare si administreaza totul dintr-un dashboard coerent, pe schema ta PostgreSQL.
        </p>
        <div class="d-flex flex-wrap gap-3 mt-4">
            <a href="<?= e(url('locatii.php')) ?>" class="btn btn-crimson btn-lg rounded-pill px-4">Exploreaza locatiile</a>
            <a href="<?= e(url('evenimente.php')) ?>" class="btn btn-soft btn-lg rounded-pill px-4">Vezi evenimentele</a>
        </div>
        <div class="metric-row">
            <div class="metric-tile">
                <strong><?= (int)$stats['locatii'] ?></strong>
                <span>locatii in sistem</span>
            </div>
            <div class="metric-tile">
                <strong><?= (int)$stats['evenimente'] ?></strong>
                <span>evenimente create</span>
            </div>
            <div class="metric-tile">
                <strong><?= (int)$stats['rezervari'] ?></strong>
                <span>rezervari inregistrate</span>
            </div>
        </div>
    </div>

    <div class="glass-panel hero-side">
        <div class="highlight-card">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-calendar-check"></i>
                <div>
                    <strong>Flux clar de rezervare</strong>
                    <div class="text-secondary-emphasis">Alegi data, intervalul orar si trimiti direct cererea pentru locatie.</div>
                </div>
            </div>
        </div>
        <div class="highlight-card">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-building"></i>
                <div>
                    <strong>Catalog vizual de locatii</strong>
                    <div class="text-secondary-emphasis">Carduri, poze, adresa, capacitate si disponibilitate pe urmatoarele zile.</div>
                </div>
            </div>
        </div>
        <div class="highlight-card">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-speedometer2"></i>
                <div>
                    <strong>Admin dashboard modern</strong>
                    <div class="text-secondary-emphasis">KPI-uri, rezervari recente, distribuire pe status si pagini mai aerisite.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mb-5">
    <div class="panel-header">
        <div>
            <h2 class="section-title mb-1">Locatii recomandate</h2>
            <p class="section-subtitle mb-0">Primele locatii din baza ta de date, afisate intr-un mod mult mai vandabil.</p>
        </div>
        <a href="<?= e(url('locatii.php')) ?>" class="btn btn-soft rounded-pill">Vezi toate</a>
    </div>

    <?php if ($locatii): ?>
        <div class="location-grid">
            <?php foreach ($locatii as $loc): ?>
                <article class="card-surface location-card">
                    <img class="location-card__image" src="<?= e(loc_image($loc['poza'] ?? '')) ?>" alt="<?= e($loc['nume_locatie']) ?>">
                    <div class="location-card__body">
                        <span class="card-tag"><i class="bi bi-geo-alt"></i> locatie premium</span>
                        <h3 class="h4 mt-3 mb-2"><?= e($loc['nume_locatie']) ?></h3>
                        <div class="location-meta mb-3">
                            <span><i class="bi bi-pin-map"></i> <?= e($loc['adresa']) ?></span>
                            <span><i class="bi bi-people"></i> <?= (int)$loc['capacitate'] ?> persoane</span>
                        </div>
                        <a class="btn btn-crimson rounded-pill" href="<?= e(url('locatie.php?id=' . (int)$loc['id_locatie'])) ?>">Vezi detalii</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="glass-panel empty-state">
            Nu exista locatii in baza de date. Adauga in PostgreSQL si ele vor aparea automat aici.
        </div>
    <?php endif; ?>
</section>

<section class="split-panel mb-5">
    <div class="glass-panel p-4 p-lg-5">
        <span class="card-tag mb-3"><i class="bi bi-layers"></i> de ce arata mai bine</span>
        <h2 class="section-title mb-3">Am scos proiectul din zona de tabele seci si l-am dus spre un flow comercial.</h2>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-item__dot"><i class="bi bi-search"></i></div>
                <div class="timeline-item__body"><strong>1. Descoperire rapida</strong><div class="text-secondary-emphasis">Utilizatorul vede imediat locatiile si evenimentele relevante.</div></div>
            </div>
            <div class="timeline-item">
                <div class="timeline-item__dot"><i class="bi bi-calendar-week"></i></div>
                <div class="timeline-item__body"><strong>2. Verificare disponibilitate</strong><div class="text-secondary-emphasis">Calendarul pe locatie arata dintr-o privire zilele ocupate si zilele libere.</div></div>
            </div>
            <div class="timeline-item">
                <div class="timeline-item__dot"><i class="bi bi-send-check"></i></div>
                <div class="timeline-item__body"><strong>3. Cerere trimisa elegant</strong><div class="text-secondary-emphasis">Formularul de rezervare este separat, clar si pregatit pentru extindere.</div></div>
            </div>
        </div>
    </div>

    <div class="glass-panel p-4 p-lg-5">
        <div class="panel-header mb-4">
            <div>
                <h2 class="section-title mb-1">Evenimente apropiate</h2>
                <p class="section-subtitle mb-0">Listare vizuala, nu tabela rece.</p>
            </div>
        </div>
        <?php if ($evenimente): ?>
            <div class="d-grid gap-3">
                <?php foreach ($evenimente as $ev): ?>
                    <article class="highlight-card">
                        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                            <div>
                                <span class="card-tag mb-2"><i class="bi bi-calendar-event"></i> <?= e(format_date_ro($ev['data_eveniment'])) ?></span>
                                <h3 class="h5 mb-2"><?= e($ev['titlu']) ?></h3>
                                <div class="inline-meta mb-2">
                                    <span><i class="bi bi-clock"></i> <?= e(substr($ev['ora_inceput'], 0, 5)) ?> - <?= e(substr($ev['ora_sfarsit'], 0, 5)) ?></span>
                                    <span><i class="bi bi-building"></i> <?= e($ev['nume_locatie']) ?></span>
                                </div>
                                <div class="text-secondary-emphasis small"><?= e(short_text((string)($ev['descriere'] ?? 'Eveniment disponibil pentru rezervare.'), 110)) ?></div>
                            </div>
                            <div class="text-end">
                                <div class="badge-soft neutral mb-2"><?= e(format_money($ev['pret'] ?? 0)) ?></div>
                                <a href="<?= e(url('locatie.php?id=' . (int)$ev['id_locatie'])) ?>" class="btn btn-soft btn-sm rounded-pill">Detalii locatie</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">Nu exista inca evenimente salvate in baza.</div>
        <?php endif; ?>
    </div>
</section>
<?php render_footer(); ?>
