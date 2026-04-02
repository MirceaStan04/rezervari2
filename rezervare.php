<?php
require_once __DIR__ . '/includes/config.php';
require 'includes/layout.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id_locatie = (int)($_GET['locatie'] ?? 0);
$data = $_GET['data'] ?? '';

if (!$id_locatie || !$data) {
    die('Date invalide pentru rezervare.');
}

$stmt = $pdo->prepare('SELECT id_locatie, nume_locatie, adresa, capacitate FROM locatii WHERE id_locatie = ?');
$stmt->execute([$id_locatie]);
$locatie = $stmt->fetch();

if (!$locatie) {
    die('Locația selectată nu există.');
}

render_header('Formular rezervare');
?>

<div class="auth-card p-4 p-lg-5" style="max-width: 760px;">
    <div class="mb-4 text-center">
        <span class="feature-icon mb-3"><i class="bi bi-journal-plus"></i></span>
        <h1 class="section-title mb-2">Formular rezervare</h1>
        <p class="section-subtitle mx-auto mb-0">Completează detaliile evenimentului pentru a trimite cererea de rezervare.</p>
    </div>

    <div class="soft-card p-3 p-lg-4 mb-4">
        <div class="row g-3">
            <div class="col-md-6"><strong>Locație:</strong><br><?= htmlspecialchars($locatie['nume_locatie']) ?></div>
            <div class="col-md-6"><strong>Data selectată:</strong><br><?= htmlspecialchars($data) ?></div>
            <div class="col-md-6"><strong>Adresă:</strong><br><?= htmlspecialchars($locatie['adresa']) ?></div>
            <div class="col-md-6"><strong>Capacitate:</strong><br><?= (int)$locatie['capacitate'] ?> persoane</div>
        </div>
    </div>

    <form method="post" action="rezervare_submit.php" class="row g-3">
        <input type="hidden" name="id_locatie" value="<?= (int)$locatie['id_locatie'] ?>">
        <input type="hidden" name="data_eveniment" value="<?= htmlspecialchars($data) ?>">

        <div class="col-12">
            <label class="form-label fw-semibold">Titlu eveniment</label>
            <input type="text" name="titlu" class="form-control" placeholder="Ex: Conferință IT" required>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Ora început</label>
            <input type="time" name="ora_inceput" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-semibold">Ora sfârșit</label>
            <input type="time" name="ora_sfarsit" class="form-control" required>
        </div>

        <div class="col-12">
            <label class="form-label fw-semibold">Descriere</label>
            <textarea name="descriere" class="form-control" rows="5" placeholder="Descrie pe scurt evenimentul"></textarea>
        </div>

        <div class="col-12 d-grid">
            <button class="btn btn-primary" type="submit"><i class="bi bi-send-check me-2"></i>Trimite rezervarea</button>
        </div>
    </form>
</div>

<?php render_footer(); ?>
