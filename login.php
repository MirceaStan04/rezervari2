<?php
require_once __DIR__ . '/includes/config.php';
require 'includes/layout.php';

$mesaj = '';
$tipMesaj = 'info';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $parola = $_POST['parola'] ?? '';

    if ($email && $parola) {
        $stmt = $pdo->prepare("SELECT * FROM utilizatori WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($parola, $user['parola'])) {
            $_SESSION['user_id'] = $user['id_utilizator'];
            $_SESSION['nume'] = $user['nume'];
            $_SESSION['rol'] = $user['rol'];
            header('Location: index.php');
            exit;
        }

        $mesaj = 'Emailul sau parola sunt incorecte.';
        $tipMesaj = 'danger';
    } else {
        $mesaj = 'Completează emailul și parola.';
        $tipMesaj = 'warning';
    }
}

render_header('Login – Rezervări Evenimente Sibiu');
?>

<div class="auth-card p-4 p-lg-5">
    <div class="text-center mb-4">
        <span class="feature-icon mb-3"><i class="bi bi-box-arrow-in-right"></i></span>
        <h1 class="section-title mb-2">Autentificare</h1>
        <p class="section-subtitle mx-auto mb-0">Intră în cont pentru a rezerva locații și a gestiona cererile tale.</p>
    </div>

    <?php if ($mesaj): ?>
        <div class="alert alert-<?= htmlspecialchars($tipMesaj) ?> rounded-4 border-0"><?= htmlspecialchars($mesaj) ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off" class="row g-3">
        <div class="col-12">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" class="form-control" placeholder="nume@email.com" required>
        </div>
        <div class="col-12">
            <label class="form-label fw-semibold">Parolă</label>
            <input type="password" name="parola" class="form-control" placeholder="Introdu parola" required>
        </div>
        <div class="col-12 d-grid">
            <button class="btn btn-primary" type="submit"><i class="bi bi-door-open me-2"></i>Login</button>
        </div>
    </form>

    <div class="text-center mt-4 text-muted">
        Nu ai cont încă?
        <a href="register.php" class="link-primary text-decoration-none fw-semibold">Creează unul aici</a>
    </div>
</div>

<?php render_footer(); ?>
