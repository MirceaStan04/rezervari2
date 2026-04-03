<?php
require_once __DIR__ . '/includes/config.php';
session_unset();
session_destroy();
session_start();
set_flash('info', 'Te-ai delogat cu succes.');
header('Location: ' . url('index.php'));
exit;
