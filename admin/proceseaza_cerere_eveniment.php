<?php
require_once __DIR__ . '/admin_check.php';

if (!event_requests_ready($pdo)) {
    set_flash('danger', 'Tabela cereri_evenimente lipseste.');
    header('Location: ' . url('admin/cereri_evenimente.php'));
    exit;
}

$idCerere = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if ($idCerere <= 0 || !in_array($action, ['approve', 'reject'], true)) {
    set_flash('warning', 'Actiune invalida.');
    header('Location: ' . url('admin/cereri_evenimente.php'));
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM cereri_evenimente WHERE id_cerere = ?');
$stmt->execute([$idCerere]);
$request = $stmt->fetch();
if (!$request) {
    set_flash('warning', 'Cererea nu a fost gasita.');
    header('Location: ' . url('admin/cereri_evenimente.php'));
    exit;
}

if (normalize_status($request['status']) !== 'in_asteptare') {
    set_flash('info', 'Cererea a fost deja procesata.');
    header('Location: ' . url('admin/cereri_evenimente.php'));
    exit;
}

try {
    $pdo->beginTransaction();

    if ($action === 'approve') {
        $conflictStmt = $pdo->prepare("SELECT COUNT(*)
                                       FROM evenimente
                                       WHERE id_locatie = ?
                                         AND data_eveniment = ?
                                         AND NOT (? >= ora_sfarsit OR ? <= ora_inceput)");
        $conflictStmt->execute([$request['id_locatie'], $request['data_eveniment'], $request['ora_inceput'], $request['ora_sfarsit']]);
        if ((int)$conflictStmt->fetchColumn() > 0) {
            throw new RuntimeException('Exista deja un eveniment publicat in acel interval.');
        }

        $insertEvent = $pdo->prepare('INSERT INTO evenimente (titlu, descriere, data_eveniment, ora_inceput, ora_sfarsit, pret, capacitate, id_locatie) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $insertEvent->execute([
            $request['titlu'],
            $request['descriere'],
            $request['data_eveniment'],
            $request['ora_inceput'],
            $request['ora_sfarsit'],
            $request['pret'],
            max(1, (int)($request['capacitate_estimata'] ?? 100)),
            $request['id_locatie'],
        ]);

        $idEveniment = fetch_last_insert_id_pg($pdo, 'evenimente', 'id_eveniment');
        $updateRequest = $pdo->prepare("UPDATE cereri_evenimente SET status = 'aprobata', id_eveniment_creat = ?, updated_at = NOW() WHERE id_cerere = ?");
        $updateRequest->execute([$idEveniment, $idCerere]);
        set_flash('success', 'Cererea a fost aprobata si transformata in eveniment public.');
    } else {
        $updateRequest = $pdo->prepare("UPDATE cereri_evenimente SET status = 'respinsa', updated_at = NOW() WHERE id_cerere = ?");
        $updateRequest->execute([$idCerere]);
        set_flash('success', 'Cererea a fost respinsa.');
    }

    $pdo->commit();
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    set_flash('danger', 'Nu am putut procesa cererea: ' . $e->getMessage());
}

header('Location: ' . url('admin/cereri_evenimente.php'));
exit;
