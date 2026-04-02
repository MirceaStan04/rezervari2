<?php
require_once __DIR__ . '/../includes/config.php';
require '../includes/layout.php';
require 'admin_check.php';

$users = $pdo->query("SELECT id_utilizator, nume, email, rol FROM utilizatori ORDER BY id_utilizator")->fetchAll();

render_header('Utilizatori – Admin');
?>

<section class="mb-4">
    <div class="section-card p-4 p-lg-5">
        <h1 class="section-title mb-2">Utilizatori în sistem</h1>
        <p class="section-subtitle mb-0">Vizualizare rapidă a conturilor create în platformă.</p>
    </div>
</section>

<?php if ($users): ?>
    <div class="table-shell">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nume</th>
                        <th>Email</th>
                        <th>Rol</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= (int)$u['id_utilizator'] ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($u['nume']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><span class="badge text-bg-dark rounded-pill px-3 py-2"><?= htmlspecialchars($u['rol']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="empty-state">Nu există utilizatori înregistrați.</div>
<?php endif; ?>

<?php render_footer(); ?>
