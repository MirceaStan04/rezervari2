<?php
function render_header(string $title = 'EventFlow – Gestiune Evenimente Sibiu'): void
{
    $loggedIn = is_logged_in();
    $nume = $loggedIn ? ($_SESSION['nume'] ?? 'Cont') : null;
    $flash = get_flash();
    $theme = isset($_COOKIE['ef-theme']) && $_COOKIE['ef-theme'] === 'light' ? 'light' : 'dark';
    ?>
<!DOCTYPE html>
<html lang="ro" data-theme="<?= $theme ?>" data-bs-theme="<?= $theme ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <meta name="description" content="Platforma de gestiune a evenimentelor si inscrierilor din Sibiu.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
</head>
<body>
<div class="app-shell">
    <header class="site-header sticky-top">
        <nav class="navbar navbar-expand-lg">
            <div class="container py-2">
                <a class="navbar-brand brand-mark" href="<?= e(url('index.php')) ?>">
                    <span class="brand-mark__icon"><i class="bi bi-calendar2-event"></i></span>
                    <span>
                        <strong>EventFlow</strong>
                        <small>gestiune evenimente Sibiu</small>
                    </span>
                </a>

                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-label="Meniu">
                    <i class="bi bi-list" style="font-size:1.4rem; color:var(--muted)"></i>
                </button>

                <div class="collapse navbar-collapse" id="navMain">
                    <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                        <li class="nav-item"><a class="nav-link px-3" href="<?= e(url('index.php')) ?>">Acasa</a></li>

                        <?php if (is_admin()): ?>
                            <li class="nav-item"><a class="nav-link px-3" href="<?= e(url('admin/index.php')) ?>">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link px-3" href="<?= e(url('admin/evenimente.php')) ?>">Evenimente</a></li>
                            <li class="nav-item"><a class="nav-link px-3" href="<?= e(url('admin/locatii.php')) ?>">Locatii</a></li>
                            <li class="nav-item"><a class="nav-link px-3" href="<?= e(url('admin/utilizatori.php')) ?>">Utilizatori</a></li>
                            <li class="nav-item"><a class="nav-link px-3" href="<?= e(url('admin/rezervari.php')) ?>">Inscrieri</a></li>
                        <?php elseif (is_organizer()): ?>
                            <li class="nav-item"><a class="nav-link px-3" href="<?= e(url('evenimente.php')) ?>">Evenimente</a></li>
                            <li class="nav-item"><a class="nav-link px-3" href="<?= e(url('locatii.php')) ?>">Locatii</a></li>
                            <li class="nav-item"><a class="nav-link px-3" href="<?= e(url('propune_eveniment.php')) ?>">Propune eveniment</a></li>
                            <li class="nav-item"><a class="nav-link px-3" href="<?= e(url('cererile-mele.php')) ?>">Cererile mele</a></li>
                        <?php elseif (is_client()): ?>
                            <li class="nav-item"><a class="nav-link px-3" href="<?= e(url('evenimente.php')) ?>">Evenimente</a></li>
                            <li class="nav-item"><a class="nav-link px-3" href="<?= e(url('locatii.php')) ?>">Locatii</a></li>
                            <li class="nav-item"><a class="nav-link px-3" href="<?= e(url('rezervarile-mele.php')) ?>">Inscrierile mele</a></li>
                        <?php else: ?>
                            <li class="nav-item"><a class="nav-link px-3" href="<?= e(url('evenimente.php')) ?>">Evenimente</a></li>
                            <li class="nav-item"><a class="nav-link px-3" href="<?= e(url('locatii.php')) ?>">Locatii</a></li>
                        <?php endif; ?>

                        <li class="nav-item ms-lg-1">
                            <button class="theme-toggle" type="button" data-theme-toggle aria-label="Schimba tema">
                                <i class="bi bi-moon-stars-fill"></i>
                                <span>Tema</span>
                            </button>
                        </li>
                        <?php if ($loggedIn): ?>
                            <li class="nav-item ms-lg-1">
                                <a href="<?= e(url('profil.php')) ?>" class="nav-user-pill text-decoration-none">
                                    <i class="bi bi-person-circle"></i>
                                    <?= e($nume) ?>
                                    <small class="role-pill"><?= e(role_label($_SESSION['rol'] ?? 'client')) ?></small>
                                </a>
                            </li>
                            <li class="nav-item ms-lg-1">
                                <a href="<?= e(url('logout.php')) ?>" class="btn btn-soft btn-sm rounded-pill px-3">Logout</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item ms-lg-1"><a href="<?= e(url('login.php')) ?>" class="btn btn-soft btn-sm rounded-pill px-3">Login</a></li>
                            <li class="nav-item ms-lg-1"><a href="<?= e(url('register.php')) ?>" class="btn btn-crimson btn-sm rounded-pill px-3">Cont nou</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="page-content">
        <div class="container py-4 py-lg-5">
            <?php if ($flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?> app-alert mb-4"><?= e($flash['message']) ?></div>
            <?php endif; ?>
<?php
}

function render_footer(): void
{
    ?>
        </div>
    </main>

    <footer class="site-footer mt-auto">
        <div class="container py-4 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <strong>EventFlow</strong>
                <div class="footer-copy">Sistem de gestiune a evenimentelor si inscrierilor.</div>
            </div>
            <div class="footer-copy">&copy; <?= date('Y') ?> EventFlow &bull; Platforma de evenimente din Sibiu</div>
        </div>
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= e(asset('assets/js/app.js')) ?>"></script>
</body>
</html>
<?php
}
