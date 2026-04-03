<?php
require_once __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/layout.php';
require_login();

$locatii = $pdo->query('SELECT id_locatie, nume_locatie, adresa, capacitate FROM locatii ORDER BY nume_locatie ASC')->fetchAll();
$selectedLocatie = (int)($_GET['locatie'] ?? ($_POST['id_locatie'] ?? 0));
$dataPreset = trim($_GET['data'] ?? ($_POST['data_eveniment'] ?? ''));

$locatieCurenta = null;
if ($selectedLocatie > 0) {
    $stmt = $pdo->prepare('SELECT id_locatie, nume_locatie, adresa, capacitate, poza FROM locatii WHERE id_locatie = ?');
    $stmt->execute([$selectedLocatie]);
    $locatieCurenta = $stmt->fetch();
}

render_header('Creeaza rezervare - Crimson Reserve');
?>
<section class="split-panel">
    <div class="glass-panel p-4 p-lg-5">
        <span class="card-tag mb-3"><i class="bi bi-journal-plus"></i> formular rezervare</span>
        <h1 class="section-title mb-2">Trimite o cerere noua</h1>
        <p class="section-subtitle mb-4">Formularul creeaza un eveniment nou si o rezervare legata de utilizatorul autentificat.</p>

        <form method="post" action="<?= e(url('rezervare_submit.php')) ?>" class="row g-3 form-card">
            <div class="col-12">
                <label class="form-label">Locatie</label>
                <select name="id_locatie" class="form-select" required>
                    <option value="">Alege locatia</option>
                    <?php foreach ($locatii as $loc): ?>
                        <option value="<?= (int)$loc['id_locatie'] ?>" <?= $selectedLocatie === (int)$loc['id_locatie'] ? 'selected' : '' ?>>
                            <?= e($loc['nume_locatie']) ?> - <?= e($loc['adresa']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-8">
                <label class="form-label">Titlu eveniment</label>
                <input type="text" name="titlu" class="form-control" required placeholder="Ex: Conferinta Tech Sibiu">
            </div>
            <div class="col-md-4">
                <label class="form-label">Numar locuri estimate</label>
                <input type="number" name="numar_locuri" class="form-control" required min="1" value="1">
            </div>
            <div class="col-md-4">
                <label class="form-label">Data</label>
                <input type="date" name="data_eveniment" class="form-control" required value="<?= e($dataPreset) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Ora inceput</label>
                <input type="time" name="ora_inceput" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Ora sfarsit</label>
                <input type="time" name="ora_sfarsit" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Pret estimat</label>
                <input type="number" step="0.01" min="0" name="pret" class="form-control" value="0">
            </div>
            <div class="col-12">
                <label class="form-label">Descriere</label>
                <textarea name="descriere" rows="5" class="form-control" placeholder="Descrie tipul de eveniment, necesitatile si contextul."></textarea>
            </div>
            <div class="col-12 d-flex flex-wrap gap-2 mt-2">
                <button class="btn btn-crimson btn-lg rounded-pill px-4" type="submit">Trimite rezervarea</button>
                <a href="<?= e(url('rezervarile-mele.php')) ?>" class="btn btn-soft btn-lg rounded-pill px-4">Vezi rezervarile mele</a>
            </div>
        </form>
    </div>

    <aside class="glass-panel p-4 p-lg-5">
        <?php if ($locatieCurenta): ?>
            <span class="card-tag mb-3"><i class="bi bi-building-check"></i> locatie selectata</span>
            <h2 class="section-title h3 mb-2"><?= e($locatieCurenta['nume_locatie']) ?></h2>
            <p class="section-subtitle mb-4"><?= e($locatieCurenta['adresa']) ?></p>
            <div class="d-grid gap-3 mb-4">
                <div class="detail-chip"><strong>Capacitate</strong><span><?= (int)$locatieCurenta['capacitate'] ?> persoane</span></div>
                <div class="detail-chip"><strong>Sugestie</strong><span>Poti seta exact intervalul dorit, iar sistemul verifica suprapunerile.</span></div>
            </div>
            <img class="location-card__image rounded-4" src="<?= e(loc_image($locatieCurenta['poza'] ?? '')) ?>" alt="<?= e($locatieCurenta['nume_locatie']) ?>">
        <?php else: ?>
            <span class="card-tag mb-3"><i class="bi bi-info-circle"></i> ce face acest formular</span>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-item__dot"><i class="bi bi-1-circle"></i></div>
                    <div class="timeline-item__body"><strong>Creeaza evenimentul</strong><div class="text-secondary-emphasis">Datele se duc in tabelul evenimente.</div></div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-item__dot"><i class="bi bi-2-circle"></i></div>
                    <div class="timeline-item__body"><strong>Leaga rezervarea de utilizator</strong><div class="text-secondary-emphasis">Se insereaza si in rezervari cu status compatibil.</div></div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-item__dot"><i class="bi bi-3-circle"></i></div>
                    <div class="timeline-item__body"><strong>Evita conflictele</strong><div class="text-secondary-emphasis">Sistemul verifica suprapunerea pe locatie, data si interval orar.</div></div>
                </div>
            </div>
        <?php endif; ?>
    </aside>
</section>
<?php render_footer(); ?>
