<?php
require_once __DIR__ . '/admin_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('admin/utilizatori.php'));
    exit;
}

$idUtilizator = (int)($_POST['id'] ?? 0);
$rol = normalize_role($_POST['rol'] ?? 'client');

if ($idUtilizator <= 0) {
    set_flash('warning', 'ID utilizator invalid.');
    header('Location: ' . url('admin/utilizatori.php'));
    exit;
}

if ($idUtilizator === (int)$_SESSION['user_id']) {
    set_flash('danger', 'Nu iti poti schimba propriul rol.');
    header('Location: ' . url('admin/utilizatori.php'));
    exit;
}

try {
    $stmt = $pdo->prepare('UPDATE utilizatori SET rol = ? WHERE id_utilizator = ?');
    $stmt->execute([$rol, $idUtilizator]);
    set_flash('success', 'Rolul utilizatorului a fost actualizat la ' . role_label($rol) . '.');
} catch (PDOException $e) {
    set_flash('danger', 'Eroare: ' . $e->getMessage());
}

header('Location: ' . url('admin/utilizatori.php'));
exit;
