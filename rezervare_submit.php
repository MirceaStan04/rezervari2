<?php
require_once __DIR__ . '/includes/config.php';
require_client();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('evenimente.php'));
    exit;
}

$idEveniment = (int)($_POST['id_eveniment'] ?? 0);
$numarLocuri = max(1, (int)($_POST['numar_locuri'] ?? 1));
$idUtilizator = (int)$_SESSION['user_id'];

if ($idEveniment <= 0) {
    set_flash('warning', 'Eveniment invalid.');
    header('Location: ' . url('evenimente.php'));
    exit;
}

$stmt = $pdo->prepare('SELECT id_eveniment, data_eveniment, capacitate FROM evenimente WHERE id_eveniment = ?');
$stmt->execute([$idEveniment]);
$event = $stmt->fetch();
if (!$event) {
    set_flash('danger', 'Evenimentul selectat nu exista.');
    header('Location: ' . url('evenimente.php'));
    exit;
}

$dupStmt = $pdo->prepare("SELECT COUNT(*) FROM rezervari WHERE id_utilizator = ? AND id_eveniment = ? AND status NOT IN ('respinsa','anulata','rejected','cancelled')");
$dupStmt->execute([$idUtilizator, $idEveniment]);
if ((int)$dupStmt->fetchColumn() > 0) {
    set_flash('warning', 'Esti deja inscris la acest eveniment.');
    header('Location: ' . url('rezervarile-mele.php'));
    exit;
}

$capacitate = (int)($event['capacitate'] ?? 0);
if ($capacitate > 0) {
    $locStmt = $pdo->prepare("SELECT COALESCE(SUM(numar_locuri), 0) FROM rezervari WHERE id_eveniment = ? AND status NOT IN ('respinsa','anulata','rejected','cancelled')");
    $locStmt->execute([$idEveniment]);
    $locuriOcupate = (int)$locStmt->fetchColumn();
    if ($locuriOcupate + $numarLocuri > $capacitate) {
        $ramase = max(0, $capacitate - $locuriOcupate);
        set_flash('danger', $ramase > 0
            ? "Nu sunt suficiente locuri disponibile. Mai sunt doar {$ramase} locuri."
            : 'Evenimentul este complet. Nu mai sunt locuri disponibile.');
        header('Location: ' . url('rezervare.php?eveniment=' . $idEveniment));
        exit;
    }
}

try {
    insert_rezervare_compatible($pdo, $idUtilizator, $idEveniment, $numarLocuri);
    set_flash('success', 'Inscrierea a fost trimisa cu succes.');
    header('Location: ' . url('rezervarile-mele.php'));
    exit;
} catch (Throwable $e) {
    set_flash('danger', 'A aparut o eroare la salvare. Verifica schema tabelului rezervari.');
    header('Location: ' . url('rezervare.php?eveniment=' . $idEveniment));
    exit;
}
