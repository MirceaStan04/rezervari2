<?php
require_once __DIR__ . '/admin_check.php';
require __DIR__ . '/../includes/layout.php';

$idEveniment = (int)($_GET['id'] ?? 0);
if ($idEveniment <= 0) {
    set_flash('warning', 'ID eveniment invalid.');
    header('Location: ' . url('admin/evenimente.php'));
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM evenimente WHERE id_eveniment = ?');
$stmt->execute([$idEveniment]);
$event = $stmt->fetch();
if (!$event) {
    set_flash('warning', 'Evenimentul nu a fost gasit.');
    header('Location: ' . url('admin/evenimente.php'));
    exit;
}

$locatii = $pdo->query('SELECT id_locatie, nume_locatie, adresa FROM locatii ORDER BY nume_locatie ASC')->fetchAll();

render_header('Admin - editeaza eveniment');
?>
<section class="mb-4">
    <span class="card-tag mb-3"><i class="bi bi-calendar4-event"></i> management evenimente</span>
    <h1 class="section-title mb-2">Editeaza eveniment</h1>
    <p class="section-subtitle">Modifica detaliile evenimentului publicat. Inscrierile existente nu sunt afectate.</p>
</section>

<div class="glass-panel p-4 p-lg-5" style="max-width:780px">
    <form method="post" action="<?= e(url('admin/salveaza_eveniment.php')) ?>" class="row g-3">
        <input type="hidden" name="id" value="<?= (int)$event['id_eveniment'] ?>">
        <div class="col-12">
            <label class="form-label">Titlu</label>
            <input type="text" name="titlu" class="form-control" required value="<?= e($event['titlu']) ?>">
        </div>
        <div class="col-12">
            <label class="form-label">Locatie</label>
            <select name="id_locatie" class="form-select" required>
                <?php foreach ($locatii as $loc): ?>
                    <option value="<?= (int)$loc['id_locatie'] ?>" <?= (int)$loc['id_locatie'] === (int)$event['id_locatie'] ? 'selected' : '' ?>>
                        <?= e($loc['nume_locatie']) ?> – <?= e($loc['adresa']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Data</label>
            <input type="date" name="data_eveniment" class="form-control" required value="<?= e($event['data_eveniment']) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Ora inceput</label>
            <input type="time" name="ora_inceput" class="form-control" required value="<?= e(substr($event['ora_inceput'], 0, 5)) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Ora sfarsit</label>
            <input type="time" name="ora_sfarsit" class="form-control" required value="<?= e(substr($event['ora_sfarsit'], 0, 5)) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Pret (lei)</label>
            <input type="number" step="0.01" min="0" name="pret" class="form-control" value="<?= e($event['pret'] ?? '0') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Capacitate (locuri)</label>
            <input type="number" min="1" name="capacitate" class="form-control" required value="<?= (int)($event['capacitate'] ?? 100) ?>">
        </div>
        <div class="col-12">
            <label class="form-label">Descriere</label>
            <textarea name="descriere" rows="5" class="form-control"><?= e($event['descriere'] ?? '') ?></textarea>
        </div>
        <div class="col-12 d-flex flex-wrap gap-2 mt-2">
            <button type="submit" class="btn btn-crimson rounded-pill px-4"><i class="bi bi-check-lg"></i> Salveaza</button>
            <a href="<?= e(url('admin/evenimente.php')) ?>" class="btn btn-soft rounded-pill px-4">Anuleaza</a>
        </div>
    </form>
</div>
<?php render_footer(); ?>
