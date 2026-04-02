<?php
require_once __DIR__ . '/includes/config.php';
require 'includes/layout.php';

if (!isset($_GET['id'])) {
    die('Locație invalidă.');
}

$id_locatie = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM locatii WHERE id_locatie = ?");
$stmt->execute([$id_locatie]);
$locatie = $stmt->fetch();

if (!$locatie) {
    die('Locația nu există.');
}

$stmt = $pdo->prepare("SELECT data_eveniment FROM evenimente WHERE id_locatie = ?");
$stmt->execute([$id_locatie]);
$zile_ocupate = $stmt->fetchAll(PDO::FETCH_COLUMN);

$calendar = [];
$azi = new DateTime();
for ($i = 0; $i < 30; $i++) {
    $zi = clone $azi;
    $zi->modify("+$i days");
    $calendar[] = $zi->format('Y-m-d');
}

render_header(htmlspecialchars($locatie['nume_locatie']) . ' – Disponibilitate');
?>

<section class="mb-4">
    <div class="section-card overflow-hidden">
        <div class="row g-0 align-items-stretch">
            <div class="col-lg-6">
                <img src="<?= htmlspecialchars($locatie['poza']) ?>" alt="<?= htmlspecialchars($locatie['nume_locatie']) ?>" class="w-100 h-100" style="object-fit: cover; min-height: 320px;">
            </div>
            <div class="col-lg-6 p-4 p-lg-5 d-flex flex-column justify-content-center">
                <span class="badge-soft mb-3"><i class="bi bi-geo-alt"></i> Detalii locație</span>
                <h1 class="section-title mb-2"><?= htmlspecialchars($locatie['nume_locatie']) ?></h1>
                <p class="section-subtitle mb-4"><i class="bi bi-geo-alt-fill me-1"></i><?= htmlspecialchars($locatie['adresa']) ?></p>

                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="soft-card p-3 h-100">
                            <div class="small text-uppercase text-muted fw-semibold mb-1">Capacitate</div>
                            <div class="fw-bold fs-5"><?= htmlspecialchars($locatie['capacitate']) ?> persoane</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="soft-card p-3 h-100">
                            <div class="small text-uppercase text-muted fw-semibold mb-1">Disponibilitate</div>
                            <div class="fw-bold fs-5">Următoarele 30 de zile</div>
                        </div>
                    </div>
                </div>

                <p class="text-muted mt-4 mb-0">Zilele verzi pot fi rezervate, iar cele roșii sunt deja ocupate de alte evenimente.</p>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
        <div>
            <h2 class="section-title mb-1">Calendar disponibilitate</h2>
            <p class="section-subtitle mb-0">Selectează o zi liberă pentru a porni cererea de rezervare.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <span class="badge text-bg-success rounded-pill px-3 py-2">Zi liberă</span>
            <span class="badge text-bg-danger rounded-pill px-3 py-2">Zi ocupată</span>
        </div>
    </div>

    <div class="row g-3">
        <?php foreach ($calendar as $zi): ?>
            <?php $ocupata = in_array($zi, $zile_ocupate, true); ?>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="calendar-day <?= $ocupata ? 'busy bg-danger text-white' : 'free bg-success text-white' ?> shadow-sm">
                    <div>
                        <div class="fw-bold fs-5 mb-1"><?= htmlspecialchars($zi) ?></div>
                        <small><?= $ocupata ? 'Deja rezervată' : 'Disponibilă pentru eveniment' ?></small>
                    </div>
                    <div class="mt-3">
                        <?php if ($ocupata): ?>
                            <span class="btn btn-light btn-sm disabled w-100">Ocupată</span>
                        <?php else: ?>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="rezervare.php?locatie=<?= $id_locatie ?>&data=<?= urlencode($zi) ?>" class="btn btn-light btn-sm w-100">Rezervă ziua</a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-outline-light btn-sm w-100">Login pentru rezervare</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php render_footer(); ?>
