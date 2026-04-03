<?php
require_once __DIR__ . '/admin_check.php';
require __DIR__ . '/../includes/layout.php';

$users = $pdo->query('SELECT id_utilizator, nume, email, rol, data_creare FROM utilizatori ORDER BY id_utilizator DESC')->fetchAll();
render_header('Admin - utilizatori');
?>
<section class="mb-4">
    <span class="card-tag mb-3"><i class="bi bi-people"></i> utilizatori</span>
    <h1 class="section-title mb-2">Conturi din sistem</h1>
    <p class="section-subtitle">Am schimbat listarea in profile cards, pastrand informatia utila din baza de date.</p>
</section>

<?php if ($users): ?>
    <div class="user-grid">
        <?php foreach ($users as $user): ?>
            <article class="card-surface user-card">
                <div class="user-card__body">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <span class="card-tag mb-2">ID user #<?= (int)$user['id_utilizator'] ?></span>
                            <h2 class="h4 mb-1"><?= e($user['nume']) ?></h2>
                            <div class="text-secondary-emphasis small"><?= e($user['email']) ?></div>
                        </div>
                        <span class="<?= ($user['rol'] ?? '') === 'admin' ? 'badge-soft success' : 'badge-soft neutral' ?>"><?= e($user['rol'] ?? 'user') ?></span>
                    </div>
                    <div class="table-lite small">
                        <table class="table-lite">
                            <tr><td class="text-secondary-emphasis">Creat la</td><td><?= e(format_date_ro($user['data_creare'] ?? '')) ?></td></tr>
                            <tr><td class="text-secondary-emphasis">Email</td><td><?= e($user['email']) ?></td></tr>
                        </table>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="glass-panel empty-state">Nu exista utilizatori inregistrati.</div>
<?php endif; ?>
<?php render_footer(); ?>
