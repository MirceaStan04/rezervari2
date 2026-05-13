<?php
require_once __DIR__ . '/includes/config.php';
require_organizer();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('propune_eveniment.php'));
    exit;
}

if (!event_requests_ready($pdo)) {
    set_flash('danger', 'Functionalitate temporar indisponibila. Contacteaza administratorul.');
    header('Location: ' . url('propune_eveniment.php'));
    exit;
}

$idLocatie = (int)($_POST['id_locatie'] ?? 0);
$titlu = trim($_POST['titlu'] ?? '');
$dataEveniment = trim($_POST['data_eveniment'] ?? '');
$oraInceput = trim($_POST['ora_inceput'] ?? '');
$oraSfarsit = trim($_POST['ora_sfarsit'] ?? '');
$descriere = trim($_POST['descriere'] ?? '');
$capacitateEstimata = max(1, (int)($_POST['capacitate_estimata'] ?? 1));
$pret = (float)($_POST['pret'] ?? 0);
$idOrganizator = (int)$_SESSION['user_id'];

if ($idLocatie <= 0 || $titlu === '' || $dataEveniment === '' || $oraInceput === '' || $oraSfarsit === '') {
    set_flash('warning', 'Completeaza toate campurile obligatorii.');
    header('Location: ' . url('propune_eveniment.php?locatie=' . $idLocatie . '&data=' . urlencode($dataEveniment)));
    exit;
}

if ($dataEveniment <= date('Y-m-d')) {
    set_flash('warning', 'Data evenimentului trebuie sa fie in viitor.');
    header('Location: ' . url('propune_eveniment.php?locatie=' . $idLocatie . '&data=' . urlencode($dataEveniment)));
    exit;
}

if ($oraInceput >= $oraSfarsit) {
    set_flash('warning', 'Intervalul orar este invalid.');
    header('Location: ' . url('propune_eveniment.php?locatie=' . $idLocatie . '&data=' . urlencode($dataEveniment)));
    exit;
}

$conflictStmt = $pdo->prepare("SELECT COUNT(*)
                               FROM evenimente
                               WHERE id_locatie = ?
                                 AND data_eveniment = ?
                                 AND NOT (? >= ora_sfarsit OR ? <= ora_inceput)");
$conflictStmt->execute([$idLocatie, $dataEveniment, $oraInceput, $oraSfarsit]);
if ((int)$conflictStmt->fetchColumn() > 0) {
    set_flash('danger', 'Exista deja un eveniment aprobat in acel interval.');
    header('Location: ' . url('propune_eveniment.php?locatie=' . $idLocatie . '&data=' . urlencode($dataEveniment)));
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO cereri_evenimente (id_organizator, id_locatie, titlu, descriere, data_eveniment, ora_inceput, ora_sfarsit, pret, capacitate_estimata, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
    $stmt->execute([$idOrganizator, $idLocatie, $titlu, $descriere, $dataEveniment, $oraInceput, $oraSfarsit, $pret, $capacitateEstimata, 'in_asteptare']);
    set_flash('success', 'Cererea de eveniment a fost trimisa si asteapta aprobarea adminului.');
    header('Location: ' . url('cererile-mele.php'));
    exit;
} catch (Throwable $e) {
    set_flash('danger', 'A aparut o eroare. Te rugam sa incerci din nou.');
    header('Location: ' . url('propune_eveniment.php?locatie=' . $idLocatie . '&data=' . urlencode($dataEveniment)));
    exit;
}
