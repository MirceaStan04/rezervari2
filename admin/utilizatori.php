<?php
require_once __DIR__ . '/admin_check.php';
require __DIR__ . '/../includes/layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $user_id = (int)($_POST['user_id'] ?? 0);
    $new_password = trim($_POST['new_password'] ?? '');
    
    if ($user_id <= 0 || $new_password === '') {
        set_flash('warning', 'ID utilizator invalid sau parola goala.');
    } else {
        try {
            $hash = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('UPDATE utilizatori SET parola = ? WHERE id_utilizator = ?');
            $stmt->execute([$hash, $user_id]);
            if ($stmt->rowCount() > 0) {
                set_flash('success', 'Parola a fost schimbata cu succes.');
            } else {
                set_flash('warning', 'Utilizatorul nu a fost gasit.');
            }
        } catch (PDOException $e) {
            set_flash('danger', 'Eroare la schimbarea parolei: ' . $e->getMessage());
        }
    }
    header('Location: ' . url('admin/utilizatori.php'));
    exit;
}

$users = $pdo->query('SELECT id_utilizator, nume, email, rol, data_creare FROM utilizatori ORDER BY id_utilizator DESC')->fetchAll();
render_header('Admin - utilizatori');
?>
<section class="mb-4">
    <span class="card-tag mb-3"><i class="bi bi-people"></i> utilizatori</span>
    <h1 class="section-title mb-2">Conturi din sistem</h1>
    <p class="section-subtitle">Vizualizeaza si administreaza conturile utilizatorilor inregistrati.</p>
</section>

<?php if ($users): ?>
    <div class="user-grid">
        <?php foreach ($users as $user): ?>
            <?php $role = normalize_role($user['rol'] ?? 'client'); ?>
            <article class="card-surface user-card">
                <div class="user-card__body">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <span class="card-tag mb-2">ID user #<?= (int)$user['id_utilizator'] ?></span>
                            <h2 class="h4 mb-1"><?= e($user['nume']) ?></h2>
                            <div class="text-secondary-emphasis small"><?= e($user['email']) ?></div>
                        </div>
                        <span class="<?= $role === 'admin' ? 'badge-soft success' : ($role === 'organizator' ? 'badge-soft warning' : 'badge-soft neutral') ?>"><?= e(role_label($user['rol'] ?? 'client')) ?></span>
                    </div>
                    <div class="table-lite small mb-3">
                        <table class="table-lite">
                            <tr><td class="text-secondary-emphasis">Creat la</td><td><?= e(format_date_ro($user['data_creare'] ?? '')) ?></td></tr>
                            <tr><td class="text-secondary-emphasis">Email</td><td><?= e($user['email']) ?></td></tr>
                            <tr><td class="text-secondary-emphasis">Rol aplicatie</td><td><?= e(role_label($user['rol'] ?? 'client')) ?></td></tr>
                        </table>
                    </div>
                    <?php $currentUserId = (int)$_SESSION['user_id']; ?>
                    <?php if ((int)$user['id_utilizator'] !== $currentUserId): ?>
                    <form method="post" action="<?= e(url('admin/schimba_rol_utilizator.php')) ?>" class="d-flex flex-wrap gap-2 align-items-center mb-2">
                        <input type="hidden" name="id" value="<?= (int)$user['id_utilizator'] ?>">
                        <select name="rol" class="form-select form-select-sm" style="width:auto">
                            <option value="client"      <?= $role === 'client'      ? 'selected' : '' ?>>Client</option>
                            <option value="organizator" <?= $role === 'organizator' ? 'selected' : '' ?>>Organizator</option>
                            <option value="admin"       <?= $role === 'admin'       ? 'selected' : '' ?>>Admin</option>
                        </select>
                        <button type="submit" class="btn btn-soft btn-sm rounded-pill">Schimba rol</button>
                        <a href="<?= e(url('admin/sterge_utilizator.php?id=' . (int)$user['id_utilizator'])) ?>"
                           class="btn btn-soft btn-sm rounded-pill text-danger"
                           onclick="return confirm('Stergi contul lui <?= e(addslashes($user['nume'])) ?>? Actiunea nu poate fi anulata.')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </form>
                    <button type="button" class="btn btn-sm btn-soft rounded-pill" onclick="togglePasswordForm(<?= $user['id_utilizator'] ?>)">
                        <i class="bi bi-key"></i> Reset parola
                    </button>
                    <form id="password-form-<?= $user['id_utilizator'] ?>" method="post" class="mt-2 d-none" onsubmit="return confirm('Schimbi parola pentru acest utilizator?')">
                        <input type="hidden" name="action" value="change_password">
                        <input type="hidden" name="user_id" value="<?= $user['id_utilizator'] ?>">
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <input type="password" class="form-control form-control-sm" style="width:180px"
                                   id="new_password_<?= $user['id_utilizator'] ?>" name="new_password" required placeholder="Parola noua">
                            <button type="submit" class="btn btn-sm btn-crimson rounded-pill">Salveaza</button>
                            <button type="button" class="btn btn-sm btn-soft rounded-pill" onclick="togglePasswordForm(<?= $user['id_utilizator'] ?>)">Anuleaza</button>
                        </div>
                    </form>
                    <?php else: ?>
                        <span class="small text-secondary-emphasis">Contul tau curent</span>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="glass-panel empty-state">Nu exista utilizatori inregistrati.</div>
<?php endif; ?>
<script>
function togglePasswordForm(userId) {
    const form = document.getElementById('password-form-' + userId);
    if (form.classList.contains('d-none')) {
        form.classList.remove('d-none');
    } else {
        form.classList.add('d-none');
        form.reset();
    }
}
</script>
<?php render_footer(); ?>
