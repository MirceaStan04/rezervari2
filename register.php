<?php
require_once __DIR__ . '/includes/config.php';
require 'includes/layout.php';

$mesaj = '';
$tipMesaj = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nume = trim($_POST['nume'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $parola = $_POST['parola'] ?? '';

    if ($nume && $email && $parola) {
        $stmt = $pdo->prepare("SELECT id_utilizator FROM utilizatori WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $mesaj = 'Există deja un cont cu acest email.';
            $tipMesaj = 'warning';
        } else {
            $hash = password_hash($parola, PASSWORD_BCRYPT);
            $insert = $pdo->prepare("INSERT INTO utilizatori (nume, email, parola, rol) VALUES (?, ?, ?, 'utilizator')");
            $insert->execute([$nume, $email, $hash]);
            $mesaj = 'Cont creat cu succes. Te poți autentifica acum.';
            $tipMesaj = 'success';
        }
    } else {
        $mesaj = 'Te rog completează toate câmpurile.';
        $tipMesaj = 'danger';
    }
}

render_header('Înregistrare – Rezervări Evenimente Sibiu');
?>

<div class="auth-card p-4 p-lg-5">
    <div class="text-center mb-4">
        <span class="feature-icon mb-3"><i class="bi bi-person-plus"></i></span>
        <h1 class="section-title mb-2">Creează cont</h1>
        <p class="section-subtitle mx-auto mb-0">Înregistrează-te pentru a putea trimite cereri de rezervare și accesa platforma complet.</p>
    </div>

    <?php if ($mesaj): ?>
        <div class="alert alert-<?= htmlspecialchars($tipMesaj) ?> rounded-4 border-0"><?= htmlspecialchars($mesaj) ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off" class="row g-3">
        <div class="col-12">
            <label class="form-label fw-semibold">Nume complet</label>
            <input type="text" name="nume" class="form-control" placeholder="Ex: Eduard Stan" required>
        </div>
        <div class="col-12">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" class="form-control" placeholder="nume@email.com" required>
        </div>
        <div class="col-12">
            <label class="form-label fw-semibold">Parolă</label>
            <input type="password" name="parola" class="form-control" placeholder="Minim o parolă sigură" required>
        </div>
        <div class="col-12 d-grid">
            <button class="btn btn-primary" type="submit"><i class="bi bi-check2-circle me-2"></i>Creează cont</button>
        </div>
    </form>

    <div class="text-center mt-4 text-muted">
        Ai deja cont?
        <a href="login.php" class="link-primary text-decoration-none fw-semibold">Autentifică-te</a>
    </div>
</div>

<?php render_footer(); ?>
