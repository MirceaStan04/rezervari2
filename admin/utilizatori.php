<?php
require_once __DIR__ . '/admin_check.php';
require __DIR__ . '/../includes/layout.php';

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
                    <form method="post" action="<?= e(url('admin/schimba_rol_utilizator.php')) ?>" class="d-flex flex-wrap gap-2 align-items-center">
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
<?php render_footer(); ?>
