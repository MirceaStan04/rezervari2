<?php
require_once __DIR__ . '/includes/config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('profil.php'));
    exit;
}

$idUtilizator = (int)$_SESSION['user_id'];
$actiune = $_POST['actiune'] ?? '';

if ($actiune === 'profil') {
    $nume  = trim($_POST['nume'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($nume === '' || $email === '') {
        set_flash('danger', 'Numele si emailul sunt obligatorii.');
        header('Location: ' . url('profil.php'));
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_flash('danger', 'Adresa de email nu este valida.');
        header('Location: ' . url('profil.php'));
        exit;
    }

    $checkStmt = $pdo->prepare('SELECT id_utilizator FROM utilizatori WHERE email = ? AND id_utilizator != ?');
    $checkStmt->execute([$email, $idUtilizator]);
    if ($checkStmt->fetch()) {
        set_flash('danger', 'Adresa de email este deja folosita de alt cont.');
        header('Location: ' . url('profil.php'));
        exit;
    }

    try {
        $stmt = $pdo->prepare('UPDATE utilizatori SET nume = ?, email = ? WHERE id_utilizator = ?');
        $stmt->execute([$nume, $email, $idUtilizator]);
        $_SESSION['nume'] = $nume;
        set_flash('success', 'Datele contului au fost actualizate.');
    } catch (PDOException $e) {
        set_flash('danger', 'Eroare la salvare: ' . $e->getMessage());
    }

} elseif ($actiune === 'parola') {
    $parolaCurenta    = $_POST['parola_curenta'] ?? '';
    $parolaNoua       = $_POST['parola_noua'] ?? '';
    $parolaConfirmare = $_POST['parola_confirmare'] ?? '';

    if ($parolaNoua !== $parolaConfirmare) {
        set_flash('danger', 'Parola noua si confirmarea nu coincid.');
        header('Location: ' . url('profil.php'));
        exit;
    }

    if (strlen($parolaNoua) < 8) {
        set_flash('danger', 'Parola noua trebuie sa aiba cel putin 8 caractere.');
        header('Location: ' . url('profil.php'));
        exit;
    }

    $stmt = $pdo->prepare('SELECT parola FROM utilizatori WHERE id_utilizator = ?');
    $stmt->execute([$idUtilizator]);
    $row = $stmt->fetch();

    if (!$row || !password_verify($parolaCurenta, $row['parola'])) {
        set_flash('danger', 'Parola curenta este incorecta.');
        header('Location: ' . url('profil.php'));
        exit;
    }

    try {
        $hash = password_hash($parolaNoua, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('UPDATE utilizatori SET parola = ? WHERE id_utilizator = ?');
        $stmt->execute([$hash, $idUtilizator]);
        set_flash('success', 'Parola a fost schimbata cu succes.');
    } catch (PDOException $e) {
        set_flash('danger', 'Eroare la schimbarea parolei.');
    }

} else {
    set_flash('warning', 'Actiune necunoscuta.');
}

header('Location: ' . url('profil.php'));
exit;
