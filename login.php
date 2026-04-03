<?php
require_once __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/layout.php';

if (is_logged_in()) {
    set_flash('info', 'Esti deja autentificat.');
    header('Location: ' . url('index.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $parola = $_POST['parola'] ?? '';

    if ($email === '' || $parola === '') {
        set_flash('warning', 'Completeaza emailul si parola.');
        header('Location: ' . url('login.php'));
        exit;
    }

    $stmt = $pdo->prepare('SELECT * FROM utilizatori WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($parola, $user['parola'])) {
        set_flash('danger', 'Credentiale invalide.');
        header('Location: ' . url('login.php'));
        exit;
    }

    $_SESSION['user_id'] = $user['id_utilizator'];
    $_SESSION['nume'] = $user['nume'];
    $_SESSION['rol'] = normalize_role($user['rol'] ?? 'client');

    set_flash('success', 'Autentificare reusita.');
    $target = $_SESSION['last_page'] ?? url('index.php');
    if (preg_match('#/(login|register)\.php$#', $target)) {
        $target = url('index.php');
    }

    header('Location: ' . $target);
    exit;
}

render_header('Login - Crimson Reserve');
?>
<section class="auth-card glass-panel p-4 p-lg-5">
    <div class="text-center mb-4">
        <span class="card-tag mb-3"><i class="bi bi-box-arrow-in-right"></i> acces in platforma</span>
        <h1 class="section-title mb-2">Bine ai revenit</h1>
        <p class="section-subtitle mb-0 mx-auto">Clientii se inscriu la evenimente, organizatorii trimit doar propuneri, iar adminul aproba.</p>
    </div>

    <form method="post" class="row g-3" autocomplete="off">
        <div class="col-12">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required placeholder="nume@email.com">
        </div>
        <div class="col-12">
            <label class="form-label">Parola</label>
            <input type="password" name="parola" class="form-control" required placeholder="Introdu parola ta">
        </div>
        <div class="col-12 d-grid gap-2 mt-2">
            <button class="btn btn-crimson btn-lg rounded-pill" type="submit">Intra in cont</button>
            <a href="<?= e(url('register.php')) ?>" class="btn btn-soft rounded-pill">Nu ai cont? Creeaza unul</a>
        </div>
    </form>
</section>
<?php render_footer(); ?>
