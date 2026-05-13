<?php
require_once __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/layout.php';

if (is_logged_in()) {
    set_flash('info', 'Ai deja un cont activ in sesiune.');
    header('Location: ' . url('index.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nume = trim($_POST['nume'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $parola = $_POST['parola'] ?? '';
    $rol = normalize_role($_POST['rol'] ?? 'client');

    if ($nume === '' || $email === '' || $parola === '') {
        set_flash('warning', 'Completeaza toate campurile.');
        header('Location: ' . url('register.php'));
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_flash('warning', 'Email invalid.');
        header('Location: ' . url('register.php'));
        exit;
    }

    $stmt = $pdo->prepare('SELECT id_utilizator FROM utilizatori WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        set_flash('warning', 'Exista deja un cont cu acest email.');
        header('Location: ' . url('register.php'));
        exit;
    }

    try {
        $hash = password_hash($parola, PASSWORD_BCRYPT);
        insert_user_compatible($pdo, $nume, $email, $hash, $rol);
        set_flash('success', 'Cont creat cu succes. Te poti autentifica acum.');
        header('Location: ' . url('login.php'));
        exit;
    } catch (PDOException $e) {
        $message = $rol === 'organizator'
            ? 'Nu am putut crea rolul de organizator. Ruleaza scriptul SQL din database/upgrade_roles_evenimente.sql ca sa adaugi rolul organizator in PostgreSQL.'
            : 'Nu am putut crea contul pe schema curenta a bazei de date. Verifica rolul/default-urile din tabelul utilizatori.';
        set_flash('danger', $message);
        header('Location: ' . url('register.php'));
        exit;
    }
}

render_header('Inregistrare - EventFlow');
?>
<section class="auth-card glass-panel p-4 p-lg-5">
    <div class="text-center mb-4">
        <span class="card-tag mb-3"><i class="bi bi-person-plus"></i> cont nou</span>
        <h1 class="section-title mb-2">Creeaza-ti profilul</h1>
        <p class="section-subtitle mb-0 mx-auto">Alege daca intri ca participant la evenimente sau ca organizator care propune evenimente spre aprobare.</p>
    </div>

    <form method="post" class="row g-3" autocomplete="off">
        <div class="col-12">
            <label class="form-label">Nume complet</label>
            <input type="text" name="nume" class="form-control" required placeholder="Ex: Mircea Popescu">
        </div>
        <div class="col-12">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required placeholder="Ex: mircea@email.com">
        </div>
        <div class="col-md-6">
            <label class="form-label">Tip cont</label>
            <select name="rol" class="form-select" required>
                <option value="client">Client / participant</option>
                <option value="organizator">Organizator evenimente</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Parola</label>
            <input type="password" name="parola" class="form-control" required placeholder="Alege o parola sigura">
        </div>
        <div class="col-12">
            <div class="notice-box">
                <strong>Roluri in platforma</strong>
                <div class="text-secondary-emphasis small mt-2">Clientul se inscrie la evenimente deja aprobate. Organizatorul nu se inscrie la evenimente, ci trimite doar cereri pentru evenimente noi, iar adminul decide daca le publica.</div>
            </div>
        </div>
        <div class="col-12 d-grid gap-2 mt-2">
            <button class="btn btn-crimson btn-lg rounded-pill" type="submit">Creeaza contul</button>
            <a href="<?= e(url('login.php')) ?>" class="btn btn-soft rounded-pill">Am deja cont</a>
        </div>
    </form>
</section>
<?php render_footer(); ?>
