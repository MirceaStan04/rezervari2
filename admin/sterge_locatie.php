<?php
require_once __DIR__ . '/admin_check.php';

$idLocatie = (int)($_GET['id'] ?? 0);
if ($idLocatie <= 0) {
    set_flash('warning', 'ID locatie invalid.');
    header('Location: ' . url('admin/locatii.php'));
    exit;
}

try {
    $stmt = $pdo->prepare('DELETE FROM locatii WHERE id_locatie = ?');
    $stmt->execute([$idLocatie]);
    if ($stmt->rowCount() > 0) {
        set_flash('success', 'Locatia a fost stearsa.');
    } else {
        set_flash('warning', 'Locatia nu a fost gasita.');
    }
} catch (PDOException $e) {
    set_flash('danger', 'Nu se poate sterge locatia (are evenimente asociate sau alta eroare).');
}

header('Location: ' . url('admin/locatii.php'));
exit;
