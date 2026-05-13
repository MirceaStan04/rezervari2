<?php
require_once __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/layout.php';
require_login();

$idUtilizator = (int)$_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT id_utilizator, nume, email, rol, data_creare FROM utilizatori WHERE id_utilizator = ?');
$stmt->execute([$idUtilizator]);
$user = $stmt->fetch();

if (!$user) {
    set_flash('danger', 'Contul nu a fost gasit.');
    header('Location: ' . url('logout.php'));
    exit;
}

render_header('Profilul meu - EventFlow');
?>
<section class="mb-4">
    <span class="card-tag mb-3"><i class="bi bi-person-circle"></i> contul meu</span>
    <h1 class="section-title mb-2">Profilul meu</h1>
    <p class="section-subtitle">Actualizeaza datele contului sau schimba parola.</p>
</section>

<div class="split-panel">
    <div class="glass-panel p-4 p-lg-5">
        <h2 class="section-title h4 mb-4">Date cont</h2>
        <form method="post" action="<?= e(url('salveaza_profil.php')) ?>" class="row g-3">
            <input type="hidden" name="actiune" value="profil">
            <div class="col-12">
                <label class="form-label">Nume</label>
                <input type="text" name="nume" class="form-control" required value="<?= e($user['nume']) ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required value="<?= e($user['email']) ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Rol</label>
                <input type="text" class="form-control locked-input" value="<?= e(role_label($user['rol'])) ?>" disabled>
            </div>
            <div class="col-12">
                <label class="form-label">Membru din</label>
                <input type="text" class="form-control locked-input" value="<?= e(format_date_ro($user['data_creare'] ?? '')) ?>" disabled>
            </div>
            <div class="col-12 mt-2">
                <button type="submit" class="btn btn-crimson rounded-pill px-4">Salveaza datele</button>
            </div>
        </form>
    </div>

    <div class="glass-panel p-4 p-lg-5">
        <h2 class="section-title h4 mb-4">Schimba parola</h2>
        <form method="post" action="<?= e(url('salveaza_profil.php')) ?>" class="row g-3">
            <input type="hidden" name="actiune" value="parola">
            <div class="col-12">
                <label class="form-label">Parola curenta</label>
                <input type="password" name="parola_curenta" class="form-control" required autocomplete="current-password">
            </div>
            <div class="col-12">
                <label class="form-label">Parola noua</label>
                <input type="password" name="parola_noua" class="form-control" required minlength="8" autocomplete="new-password">
            </div>
            <div class="col-12">
                <label class="form-label">Confirma parola noua</label>
                <input type="password" name="parola_confirmare" class="form-control" required minlength="8" autocomplete="new-password">
            </div>
            <div class="col-12 mt-2">
                <button type="submit" class="btn btn-crimson rounded-pill px-4">Schimba parola</button>
            </div>
        </form>
    </div>
</div>
<?php render_footer(); ?>
