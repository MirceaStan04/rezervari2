<?php
require_once __DIR__ . '/admin_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('admin/locatii.php'));
    exit;
}

$idLocatie  = (int)($_POST['id'] ?? 0);
$numeLocatie = trim($_POST['nume_locatie'] ?? '');
$adresa      = trim($_POST['adresa'] ?? '');
$capacitate  = max(1, (int)($_POST['capacitate'] ?? 1));
$poza        = trim($_POST['poza'] ?? '') ?: null;

if ($numeLocatie === '' || $adresa === '') {
    set_flash('danger', 'Numele si adresa sunt obligatorii.');
    $back = $idLocatie > 0
        ? url('admin/form_locatie.php?id=' . $idLocatie)
        : url('admin/form_locatie.php');
    header('Location: ' . $back);
    exit;
}

try {
    if ($idLocatie > 0) {
        $stmt = $pdo->prepare('UPDATE locatii SET nume_locatie = ?, adresa = ?, capacitate = ?, poza = ? WHERE id_locatie = ?');
        $stmt->execute([$numeLocatie, $adresa, $capacitate, $poza, $idLocatie]);
        set_flash('success', 'Locatia a fost actualizata.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO locatii (nume_locatie, adresa, capacitate, poza) VALUES (?, ?, ?, ?)');
        $stmt->execute([$numeLocatie, $adresa, $capacitate, $poza]);
        set_flash('success', 'Locatia a fost adaugata.');
    }
} catch (PDOException $e) {
    set_flash('danger', 'Eroare la salvare: ' . $e->getMessage());
}

header('Location: ' . url('admin/locatii.php'));
exit;
