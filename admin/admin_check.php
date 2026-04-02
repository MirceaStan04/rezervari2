<?php
require_once __DIR__ . '/../includes/config.php';

// Protecție: doar adminul poate continua
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    die('<h3 style="color:red;">Acces interzis. Doar administratorii pot accesa această pagină.</h3>');
}
