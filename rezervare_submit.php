<?php
require_once __DIR__ . '/includes/config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('rezervare.php'));
    exit;
}

$idLocatie = (int)($_POST['id_locatie'] ?? 0);
$titlu = trim($_POST['titlu'] ?? '');
$dataEveniment = trim($_POST['data_eveniment'] ?? '');
$oraInceput = trim($_POST['ora_inceput'] ?? '');
$oraSfarsit = trim($_POST['ora_sfarsit'] ?? '');
$descriere = trim($_POST['descriere'] ?? '');
$numarLocuri = max(1, (int)($_POST['numar_locuri'] ?? 1));
$pret = (float)($_POST['pret'] ?? 0);
$idUtilizator = (int)$_SESSION['user_id'];

if ($idLocatie <= 0 || $titlu === '' || $dataEveniment === '' || $oraInceput === '' || $oraSfarsit === '') {
    set_flash('warning', 'Completeaza toate campurile obligatorii.');
    header('Location: ' . url('rezervare.php?locatie=' . $idLocatie . '&data=' . urlencode($dataEveniment)));
    exit;
}

if ($oraInceput >= $oraSfarsit) {
    set_flash('warning', 'Intervalul orar este invalid.');
    header('Location: ' . url('rezervare.php?locatie=' . $idLocatie . '&data=' . urlencode($dataEveniment)));
    exit;
}

$conflictStmt = $pdo->prepare("SELECT COUNT(*)
                               FROM evenimente
                               WHERE id_locatie = ?
                                 AND data_eveniment = ?
                                 AND NOT (? >= ora_sfarsit OR ? <= ora_inceput)");
$conflictStmt->execute([$idLocatie, $dataEveniment, $oraInceput, $oraSfarsit]);
if ((int)$conflictStmt->fetchColumn() > 0) {
    set_flash('danger', 'Exista deja un eveniment in acel interval.');
    header('Location: ' . url('rezervare.php?locatie=' . $idLocatie . '&data=' . urlencode($dataEveniment)));
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('INSERT INTO evenimente (titlu, descriere, data_eveniment, ora_inceput, ora_sfarsit, pret, id_locatie)
                           VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$titlu, $descriere, $dataEveniment, $oraInceput, $oraSfarsit, $pret, $idLocatie]);
    $idEveniment = (int)$pdo->lastInsertId();

    insert_rezervare_compatible($pdo, $idUtilizator, $idEveniment, $numarLocuri);

    $pdo->commit();
    set_flash('success', 'Rezervarea a fost salvata cu succes.');
    header('Location: ' . url('rezervarile-mele.php'));
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    set_flash('danger', 'A aparut o eroare la salvare. Verifica schema tabelelor evenimente si rezervari.');
    header('Location: ' . url('rezervare.php?locatie=' . $idLocatie . '&data=' . urlencode($dataEveniment)));
    exit;
}
