<?php
require_once __DIR__ . '/admin_check.php';

$idUtilizator = (int)($_GET['id'] ?? 0);
if ($idUtilizator <= 0) {
    set_flash('warning', 'ID utilizator invalid.');
    header('Location: ' . url('admin/utilizatori.php'));
    exit;
}

if ($idUtilizator === (int)$_SESSION['user_id']) {
    set_flash('danger', 'Nu iti poti sterge propriul cont din panoul de admin.');
    header('Location: ' . url('admin/utilizatori.php'));
    exit;
}

try {
    $stmt = $pdo->prepare('DELETE FROM utilizatori WHERE id_utilizator = ?');
    $stmt->execute([$idUtilizator]);
    if ($stmt->rowCount() > 0) {
        set_flash('success', 'Contul utilizatorului a fost sters.');
    } else {
        set_flash('warning', 'Utilizatorul nu a fost gasit.');
    }
} catch (PDOException $e) {
    set_flash('danger', 'Eroare la stergere: ' . $e->getMessage());
}

header('Location: ' . url('admin/utilizatori.php'));
exit;
