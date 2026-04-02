<?php
require_once __DIR__ . '/includes/config.php';

if (!isset($_SESSION['user_id'])) {
    die('Trebuie sa fii autentificat pentru a face o rezervare.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$id_locatie   = (int)($_POST['id_locatie'] ?? 0);
$titlu        = trim($_POST['titlu'] ?? '');
$data_ev      = $_POST['data_eveniment'] ?? '';
$ora_i        = $_POST['ora_inceput'] ?? '';
$ora_f        = $_POST['ora_sfarsit'] ?? '';
$descriere    = trim($_POST['descriere'] ?? '');
$id_user      = $_SESSION['user_id'];

if (!$id_locatie || !$titlu || !$data_ev || !$ora_i || !$ora_f) {
    die('Campuri lipsa pentru rezervare.');
}

$stmt = $pdo->prepare("
    INSERT INTO evenimente (titlu, descriere, data_eveniment, ora_inceput, ora_sfarsit, pret, id_locatie)
    VALUES (?, ?, ?, ?, ?, 0, ?)
");
$stmt->execute([$titlu, $descriere, $data_ev, $ora_i, $ora_f, $id_locatie]);

$id_eveniment = $pdo->lastInsertId();

$stmt2 = $pdo->prepare("
    INSERT INTO rezervari (id_utilizator, id_eveniment, numar_locuri, status)
    VALUES (?, ?, 1, 'activa')
");
$stmt2->execute([$id_user, $id_eveniment]);

require 'includes/layout.php';
render_header('Rezervare realizata');
?>
<div class="alert alert-success mt-3">
    Rezervarea a fost inregistrata cu succes pentru evenimentul: <strong><?= htmlspecialchars($titlu) ?></strong>.
</div>
<a href="index.php" class="btn btn-primary mt-2">Inapoi la pagina principala</a>
<?php render_footer(); ?>
