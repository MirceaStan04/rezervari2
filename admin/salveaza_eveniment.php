<?php
require_once __DIR__ . '/admin_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('admin/evenimente.php'));
    exit;
}

$idEveniment   = (int)($_POST['id'] ?? 0);
$titlu         = trim($_POST['titlu'] ?? '');
$idLocatie     = (int)($_POST['id_locatie'] ?? 0);
$dataEveniment = trim($_POST['data_eveniment'] ?? '');
$oraInceput    = trim($_POST['ora_inceput'] ?? '');
$oraSfarsit    = trim($_POST['ora_sfarsit'] ?? '');
$pret          = max(0, (float)($_POST['pret'] ?? 0));
$capacitate    = max(1, (int)($_POST['capacitate'] ?? 1));
$descriere     = trim($_POST['descriere'] ?? '') ?: null;

if ($idEveniment <= 0 || $titlu === '' || $idLocatie <= 0 || $dataEveniment === '' || $oraInceput === '' || $oraSfarsit === '') {
    set_flash('danger', 'Completeaza toate campurile obligatorii.');
    header('Location: ' . url('admin/editeaza_eveniment.php?id=' . $idEveniment));
    exit;
}

if ($dataEveniment <= date('Y-m-d')) {
    set_flash('warning', 'Data evenimentului trebuie sa fie in viitor.');
    header('Location: ' . url('admin/editeaza_eveniment.php?id=' . $idEveniment));
    exit;
}

try {
    $stmt = $pdo->prepare('UPDATE evenimente SET titlu=?, descriere=?, data_eveniment=?, ora_inceput=?, ora_sfarsit=?, pret=?, capacitate=?, id_locatie=? WHERE id_eveniment=?');
    $stmt->execute([$titlu, $descriere, $dataEveniment, $oraInceput, $oraSfarsit, $pret, $capacitate, $idLocatie, $idEveniment]);
    set_flash('success', 'Evenimentul a fost actualizat.');
} catch (PDOException $e) {
    set_flash('danger', 'Eroare la salvare: ' . $e->getMessage());
    header('Location: ' . url('admin/editeaza_eveniment.php?id=' . $idEveniment));
    exit;
}

header('Location: ' . url('admin/evenimente.php'));
exit;
