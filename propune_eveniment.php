<?php
require_once __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/layout.php';
require_organizer();

$locatii = $pdo->query('SELECT id_locatie, nume_locatie, adresa, capacitate FROM locatii ORDER BY nume_locatie ASC')->fetchAll();
$selectedLocatie = (int)($_GET['locatie'] ?? ($_POST['id_locatie'] ?? 0));
$dataPreset = trim($_GET['data'] ?? ($_POST['data_eveniment'] ?? ''));
$hasRequestsTable = event_requests_ready($pdo);

$locatieCurenta = null;
if ($selectedLocatie > 0) {
    $stmt = $pdo->prepare('SELECT id_locatie, nume_locatie, adresa, capacitate, poza FROM locatii WHERE id_locatie = ?');
    $stmt->execute([$selectedLocatie]);
    $locatieCurenta = $stmt->fetch();
}

render_header('Propune eveniment - EventFlow');
?>
<section class="split-panel">
    <div class="glass-panel p-4 p-lg-5">
        <span class="card-tag mb-3"><i class="bi bi-journal-plus"></i> propunere organizator</span>
        <h1 class="section-title mb-2">Trimite un eveniment nou</h1>
        <p class="section-subtitle mb-4">Tu propui datele evenimentului, adminul il aproba, iar dupa aprobare clientii il vor vedea public si se vor putea inscrie.</p>

        <?php if (!$hasRequestsTable): ?>
            <div class="notice-box warning-box mb-4">
                <strong>Functionalitate temporar indisponibila</strong>
                <div class="text-secondary-emphasis small mt-2">Nu poti trimite cereri momentan. Contacteaza administratorul platformei.</div>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= e(url('trimite_cerere_eveniment.php')) ?>" class="row g-3 form-card">
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
                <label class="form-label">Capacitate estimata</label>
                <input type="number" name="capacitate_estimata" class="form-control" required min="1" value="30">
            </div>
            <div class="col-md-4">
                <label class="form-label">Data</label>
                <input type="date" name="data_eveniment" class="form-control" required
                       min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                       value="<?= e($dataPreset) ?>">
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
                <label class="form-label">Pret propus</label>
                <input type="number" step="0.01" min="0" name="pret" class="form-control" value="0">
            </div>
            <div class="col-12">
                <label class="form-label">Descriere</label>
                <textarea name="descriere" rows="5" class="form-control" placeholder="Descrie formatul evenimentului, publicul si nevoile logistice."></textarea>
            </div>
            <div class="col-12 d-flex flex-wrap gap-2 mt-2">
                <button class="btn btn-crimson btn-lg rounded-pill px-4" type="submit" <?= !$hasRequestsTable ? 'disabled' : '' ?>>Trimite cererea</button>
                <a href="<?= e(url('cererile-mele.php')) ?>" class="btn btn-soft btn-lg rounded-pill px-4">Vezi cererile mele</a>
            </div>
        </form>
    </div>

    <aside class="glass-panel p-4 p-lg-5">
        <?php if ($locatieCurenta): ?>
            <span class="card-tag mb-3"><i class="bi bi-building-check"></i> locatie selectata</span>
            <h2 class="section-title h3 mb-2"><?= e($locatieCurenta['nume_locatie']) ?></h2>
            <p class="section-subtitle mb-4"><?= e($locatieCurenta['adresa']) ?></p>
            <div class="d-grid gap-3 mb-4">
                <div class="detail-chip"><strong>Capacitate locatie</strong><span><?= (int)$locatieCurenta['capacitate'] ?> persoane</span></div>
                <div class="detail-chip"><strong>Ce urmeaza</strong><span>Dupa aprobare, clientii se vor putea inscrie direct la eveniment.</span></div>
            </div>
            <img class="location-card__image rounded-4" src="<?= e(loc_image($locatieCurenta['poza'] ?? '')) ?>" alt="<?= e($locatieCurenta['nume_locatie']) ?>">
        <?php else: ?>
            <span class="card-tag mb-3"><i class="bi bi-info-circle"></i> noul workflow</span>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-item__dot"><i class="bi bi-1-circle"></i></div>
                    <div class="timeline-item__body"><strong>Trimiti cererea</strong><div class="text-secondary-emphasis">Evenimentul intra mai intai la aprobare.</div></div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-item__dot"><i class="bi bi-2-circle"></i></div>
                    <div class="timeline-item__body"><strong>Adminul o valideaza</strong><div class="text-secondary-emphasis">La aprobare, evenimentul apare public pe platforma.</div></div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-item__dot"><i class="bi bi-3-circle"></i></div>
                    <div class="timeline-item__body"><strong>Publicul se inscrie</strong><div class="text-secondary-emphasis">Participantii pot rezerva locuri direct din pagina evenimentului.</div></div>
                </div>
            </div>
        <?php endif; ?>
    </aside>
</section>
<?php render_footer(); ?>
