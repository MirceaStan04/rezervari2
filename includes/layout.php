<?php
function render_header(string $title = 'Rezervari Evenimente Sibiu'): void
{
    $loggedIn = is_logged_in();
    $nume = $loggedIn ? ($_SESSION['nume'] ?? 'Cont') : null;
    $admin = is_admin();
    $flash = get_flash();
    ?>
<!DOCTYPE html>
<html lang="ro" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <meta name="description" content="Platforma moderna pentru rezervari de evenimente, locatii si administrare in Sibiu.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
</head>
<body>
<div class="app-shell">
    <header class="site-header sticky-top">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container py-2">
                <a class="navbar-brand brand-mark" href="<?= e(url('index.php')) ?>">
                    <span class="brand-mark__icon"><i class="bi bi-calendar2-heart"></i></span>
                    <span>
                        <strong>Crimson Reserve</strong>
                        <small>evenimente & locatii</small>
                    </span>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navMain">
                    <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                        <li class="nav-item"><a class="nav-link" href="<?= e(url('index.php')) ?>">Acasa</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= e(url('locatii.php')) ?>">Locatii</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= e(url('evenimente.php')) ?>">Evenimente</a></li>
                        <?php if ($loggedIn): ?>
                            <li class="nav-item"><a class="nav-link" href="<?= e(url('rezervarile-mele.php')) ?>">Rezervarile mele</a></li>
                        <?php endif; ?>
                        <?php if ($admin): ?>
                            <li class="nav-item"><a class="nav-link" href="<?= e(url('admin/index.php')) ?>">Admin</a></li>
                        <?php endif; ?>
                        <li class="nav-item ms-lg-2">
                            <button class="theme-toggle" type="button" data-theme-toggle aria-label="Schimba tema">
                                <i class="bi bi-moon-stars-fill"></i>
                                <span>Tema</span>
                            </button>
                        </li>
                        <?php if ($loggedIn): ?>
                            <li class="nav-item ms-lg-2">
                                <span class="nav-user-pill"><i class="bi bi-person-circle"></i> <?= e($nume) ?></span>
                            </li>
                            <li class="nav-item ms-lg-2">
                                <a href="<?= e(url('logout.php')) ?>" class="btn btn-outline-light btn-sm rounded-pill px-3">Logout</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item ms-lg-2"><a href="<?= e(url('login.php')) ?>" class="btn btn-outline-light btn-sm rounded-pill px-3">Login</a></li>
                            <li class="nav-item ms-lg-2"><a href="<?= e(url('register.php')) ?>" class="btn btn-crimson btn-sm rounded-pill px-3">Creeaza cont</a></li>
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
                <strong>Crimson Reserve</strong>
                <div class="footer-copy">Platforma pentru rezervari de evenimente in Sibiu.</div>
            </div>
            <div class="footer-copy">&copy; <?= date('Y') ?> • proiect cu PostgreSQL + PHP + Bootstrap</div>
        </div>
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= e(asset('assets/js/app.js')) ?>"></script>
</body>
</html>
<?php
}
