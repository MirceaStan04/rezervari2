<?php
require_once __DIR__ . '/admin_check.php';

$idRezervare = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if ($idRezervare <= 0 || !in_array($action, ['approve', 'reject'], true)) {
    set_flash('warning', 'Cerere invalida pentru actualizare status.');
    header('Location: ' . url('admin/rezervari.php'));
    exit;
}

if (update_rezervare_status_compatible($pdo, $idRezervare, $action)) {
    set_flash('success', 'Status actualizat cu succes.');
} else {
    set_flash('danger', 'Nu am putut actualiza statusul pe schema curenta a bazei de date.');
}

header('Location: ' . url('admin/rezervari.php'));
exit;
