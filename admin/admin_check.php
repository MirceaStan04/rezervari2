<?php
require_once __DIR__ . '/../includes/config.php';

if (!is_admin()) {
    set_flash('danger', 'Acces interzis. Doar administratorii pot intra in aceasta zona.');
    header('Location: ' . url('login.php'));
    exit;
}
