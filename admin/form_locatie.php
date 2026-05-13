<?php
require_once __DIR__ . '/admin_check.php';
require __DIR__ . '/../includes/layout.php';

$idLocatie = (int)($_GET['id'] ?? 0);
$locatie = null;

if ($idLocatie > 0) {
    $stmt = $pdo->prepare('SELECT * FROM locatii WHERE id_locatie = ?');
    $stmt->execute([$idLocatie]);
    $locatie = $stmt->fetch();
    if (!$locatie) {
        set_flash('warning', 'Locatia nu a fost gasita.');
        header('Location: ' . url('admin/locatii.php'));
        exit;
    }
}

$titluPagina = $locatie ? 'Editeaza locatie' : 'Adauga locatie noua';
render_header('Admin - ' . $titluPagina);
?>
<section class="mb-4">
    <span class="card-tag mb-3"><i class="bi bi-buildings"></i> management locatii</span>
    <h1 class="section-title mb-2"><?= e($titluPagina) ?></h1>
    <p class="section-subtitle">Completeaza detaliile locatiei. Capacitatea reprezinta numarul maxim de persoane.</p>
</section>

<div class="glass-panel p-4 p-lg-5" style="max-width:680px">
    <form method="post" action="<?= e(url('admin/salveaza_locatie.php')) ?>" class="row g-3">
        <input type="hidden" name="id" value="<?= (int)($locatie['id_locatie'] ?? 0) ?>">
        <div class="col-12">
            <label class="form-label">Nume locatie</label>
            <input type="text" name="nume_locatie" class="form-control" required
                   value="<?= e($locatie['nume_locatie'] ?? '') ?>" placeholder="Ex: Sala Thalia">
        </div>
        <div class="col-12">
            <label class="form-label">Adresa</label>
            <input type="text" name="adresa" class="form-control" required
                   value="<?= e($locatie['adresa'] ?? '') ?>" placeholder="Ex: Str. Papiu Ilarian 2, Sibiu">
        </div>
        <div class="col-md-6">
            <label class="form-label">Capacitate (persoane)</label>
            <input type="number" name="capacitate" class="form-control" required min="1"
                   value="<?= (int)($locatie['capacitate'] ?? 100) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">URL imagine <small class="text-secondary-emphasis">(optional)</small></label>
            <input type="text" name="poza" class="form-control"
                   value="<?= e($locatie['poza'] ?? '') ?>" placeholder="https://...">
        </div>
        <div class="col-12 d-flex flex-wrap gap-2 mt-2">
            <button type="submit" class="btn btn-crimson rounded-pill px-4">
                <i class="bi bi-check-lg"></i> <?= $locatie ? 'Salveaza modificarile' : 'Adauga locatia' ?>
            </button>
            <a href="<?= e(url('admin/locatii.php')) ?>" class="btn btn-soft rounded-pill px-4">Anuleaza</a>
        </div>
    </form>
</div>
<?php render_footer(); ?>
