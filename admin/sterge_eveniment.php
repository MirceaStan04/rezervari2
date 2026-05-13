<?php
require_once __DIR__ . '/admin_check.php';

$idEveniment = (int)($_GET['id'] ?? 0);
if ($idEveniment <= 0) {
    set_flash('warning', 'ID eveniment invalid.');
    header('Location: ' . url('admin/evenimente.php'));
    exit;
}

try {
    $stmt = $pdo->prepare('DELETE FROM evenimente WHERE id_eveniment = ?');
    $stmt->execute([$idEveniment]);
    if ($stmt->rowCount() > 0) {
        set_flash('success', 'Evenimentul si inscrierile asociate au fost sterse.');
    } else {
        set_flash('warning', 'Evenimentul nu a fost gasit.');
    }
} catch (PDOException $e) {
    set_flash('danger', 'Eroare la stergere: ' . $e->getMessage());
}

header('Location: ' . url('admin/evenimente.php'));
exit;
