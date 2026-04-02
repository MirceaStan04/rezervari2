<?php
require_once __DIR__ . '/../includes/config.php';
require '../includes/layout.php';
require 'admin_check.php';

$rezervari = $pdo->query("
    SELECT r.*, u.nume AS nume_user, e.titlu AS titlu_eveniment
    FROM rezervari r
    JOIN utilizatori u ON r.id_utilizator = u.id_utilizator
    JOIN evenimente e ON r.id_eveniment = e.id_eveniment
    ORDER BY r.id_rezervare DESC
")->fetchAll();

render_header('Gestionare Rezervări');
?>

<section class="mb-4">
    <div class="section-card p-4 p-lg-5">
        <h1 class="section-title mb-2">Gestionare rezervări</h1>
        <p class="section-subtitle mb-0">Monitorizezi utilizatorul, evenimentul, statusul și numărul de locuri rezervate.</p>
    </div>
</section>

<?php if ($rezervari): ?>
    <div class="table-shell">
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Utilizator</th>
                        <th>Eveniment</th>
                        <th>Status</th>
                        <th>Locuri</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rezervari as $r): ?>
                        <tr>
                            <td><?= (int)$r['id_rezervare'] ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($r['nume_user']) ?></td>
                            <td><?= htmlspecialchars($r['titlu_eveniment']) ?></td>
                            <td>
                                <span class="badge rounded-pill text-bg-<?= $r['status'] === 'activa' ? 'success' : 'secondary' ?> px-3 py-2">
                                    <?= htmlspecialchars($r['status']) ?>
                                </span>
                            </td>
                            <td><?= (int)$r['numar_locuri'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="empty-state">Nu există rezervări înregistrate.</div>
<?php endif; ?>

<?php render_footer(); ?>
