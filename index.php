<?php
require_once __DIR__ . '/includes/config.php';
require 'includes/layout.php';

$locatii = $pdo->query("SELECT id_locatie, nume_locatie, adresa, capacitate, poza FROM locatii ORDER BY id_locatie ASC LIMIT 3")->fetchAll();
$evenimente = $pdo->query("
    SELECT e.id_eveniment, e.titlu, e.data_eveniment, e.ora_inceput, e.ora_sfarsit, l.nume_locatie
    FROM evenimente e
    JOIN locatii l ON e.id_locatie = l.id_locatie
    ORDER BY e.data_eveniment ASC, e.ora_inceput ASC
    LIMIT 4
")->fetchAll();

render_header('Acasă – Rezervări Evenimente Sibiu');
?>

<section class="hero-surface mb-5">
    <div class="row align-items-center g-4 position-relative">
        <div class="col-lg-7">
            <span class="badge-soft bg-white bg-opacity-10 text-white border border-white border-opacity-10 mb-3">
                <i class="bi bi-stars"></i> Platformă modernă pentru organizare rapidă
            </span>
            <h1 class="hero-title mb-3">Rezervă locații pentru evenimente în Sibiu, simplu și clar.</h1>
            <p class="hero-subtitle mb-4">
                Găsești rapid locații, verifici disponibilitatea pe zile și înregistrezi cereri de rezervare într-o interfață curată,
                potrivită atât pentru utilizatori, cât și pentru administratori.
            </p>
            <div class="d-flex flex-wrap gap-3 mb-4">
                <a href="locatii.php" class="btn btn-light btn-lg btn-pill"><i class="bi bi-building me-2"></i>Vezi locațiile</a>
                <a href="evenimente.php" class="btn btn-outline-light btn-lg btn-pill"><i class="bi bi-calendar-event me-2"></i>Explorează evenimentele</a>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <span class="stat-chip"><i class="bi bi-check-circle"></i> Disponibilitate pe 30 de zile</span>
                <span class="stat-chip"><i class="bi bi-shield-check"></i> Acces separat pentru admin</span>
                <span class="stat-chip"><i class="bi bi-lightning-charge"></i> Rezervare rapidă</span>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="section-card p-4 p-lg-5 text-dark h-100">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="feature-icon"><i class="bi bi-person-workspace"></i></span>
                    <div>
                        <h2 class="h4 fw-bold mb-1">Totul într-un singur loc</h2>
                        <p class="text-muted mb-0">Cont utilizator, evenimente, locații și administrare.</p>
                    </div>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-12">
                        <div class="soft-card p-3">
                            <strong>Pasul 1</strong>
                            <p class="text-muted mb-0">Alegi locația potrivită și vezi rapid dacă este ocupată.</p>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="soft-card p-3">
                            <strong>Pasul 2</strong>
                            <p class="text-muted mb-0">Completezi datele evenimentului și trimiți cererea de rezervare.</p>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="soft-card p-3">
                            <strong>Pasul 3</strong>
                            <p class="text-muted mb-0">Administratorul verifică totul din panoul de control.</p>
                        </div>
                    </div>
                </div>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="d-grid gap-2 mt-4">
                        <a href="login.php" class="btn btn-primary"><i class="bi bi-box-arrow-in-right me-2"></i>Intră în cont</a>
                        <a href="register.php" class="btn btn-outline-dark"><i class="bi bi-person-plus me-2"></i>Creează cont nou</a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success mt-4 mb-0 rounded-4 border-0">
                        Ești autentificat. Poți merge direct la locații și la rezervări.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="mb-5">
    <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
        <div>
            <h2 class="section-title mb-1">De ce merită folosit proiectul</h2>
            <p class="section-subtitle mb-0">Aspect mai curat, structură mai bună și informația importantă la vedere.</p>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="soft-card p-4 h-100">
                <span class="info-icon mb-3"><i class="bi bi-ui-checks-grid"></i></span>
                <h3 class="h5 fw-bold">Interfață mai clară</h3>
                <p class="text-muted mb-0">Paginile sunt separate logic și informația esențială se vede imediat, fără aglomerație.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="soft-card p-4 h-100">
                <span class="info-icon mb-3"><i class="bi bi-calendar-week"></i></span>
                <h3 class="h5 fw-bold">Disponibilitate rapidă</h3>
                <p class="text-muted mb-0">Poți verifica zilele libere pentru fiecare locație și porni rezervarea din același ecran.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="soft-card p-4 h-100">
                <span class="info-icon mb-3"><i class="bi bi-speedometer2"></i></span>
                <h3 class="h5 fw-bold">Panou admin separat</h3>
                <p class="text-muted mb-0">Administratorul vede centralizat locațiile, evenimentele, rezervările și utilizatorii.</p>
            </div>
        </div>
    </div>
</section>

<section class="mb-5">
    <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
        <div>
            <h2 class="section-title mb-1">Locații recomandate</h2>
            <p class="section-subtitle mb-0">Primele locații din sistem, prezentate într-un format mai prietenos.</p>
        </div>
        <a href="locatii.php" class="btn btn-outline-primary btn-pill">Toate locațiile</a>
    </div>

    <?php if ($locatii): ?>
        <div class="row g-4">
            <?php foreach ($locatii as $loc): ?>
                <div class="col-md-6 col-xl-4">
                    <article class="location-card h-100">
                        <?php if (!empty($loc['poza'])): ?>
                            <img src="<?= htmlspecialchars($loc['poza']) ?>" alt="<?= htmlspecialchars($loc['nume_locatie']) ?>" class="card-cover">
                        <?php else: ?>
                            <div class="card-cover d-flex align-items-center justify-content-center text-muted">
                                <i class="bi bi-image fs-1"></i>
                            </div>
                        <?php endif; ?>
                        <div class="p-4">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                <div>
                                    <h3 class="h4 fw-bold mb-1"><?= htmlspecialchars($loc['nume_locatie']) ?></h3>
                                    <p class="text-muted mb-0"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($loc['adresa']) ?></p>
                                </div>
                                <span class="badge-soft"><i class="bi bi-people"></i><?= (int)$loc['capacitate'] ?> locuri</span>
                            </div>
                            <a href="locatie.php?id=<?= (int)$loc['id_locatie'] ?>" class="btn btn-primary w-100">Vezi disponibilitatea</a>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">Nu există locații de afișat momentan.</div>
    <?php endif; ?>
</section>

<section>
    <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
        <div>
            <h2 class="section-title mb-1">Evenimente viitoare</h2>
            <p class="section-subtitle mb-0">Programările din sistem, afișate mai clar și mai elegant.</p>
        </div>
        <a href="evenimente.php" class="btn btn-outline-primary btn-pill">Toate evenimentele</a>
    </div>

    <?php if ($evenimente): ?>
        <div class="row g-3">
            <?php foreach ($evenimente as $ev): ?>
                <div class="col-lg-6">
                    <div class="soft-card p-4 h-100">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <h3 class="h5 fw-bold mb-2"><?= htmlspecialchars($ev['titlu']) ?></h3>
                                <p class="text-muted mb-1"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($ev['nume_locatie']) ?></p>
                                <p class="text-muted mb-0"><i class="bi bi-clock me-1"></i><?= htmlspecialchars($ev['ora_inceput']) ?> - <?= htmlspecialchars($ev['ora_sfarsit']) ?></p>
                            </div>
                            <span class="badge text-bg-primary rounded-pill px-3 py-2"><?= htmlspecialchars($ev['data_eveniment']) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">Momentan nu sunt evenimente înregistrate.</div>
    <?php endif; ?>
</section>

<?php render_footer(); ?>
