<?php
function render_header(string $title = 'Rezervari Evenimente Sibiu')
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $loggedIn = isset($_SESSION['user_id']);
    $nume = $loggedIn ? htmlspecialchars($_SESSION['nume']) : null;
    $isAdmin = $loggedIn && isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
    $currentPath = strtok($_SERVER['REQUEST_URI'] ?? '', '?') ?: '';
    $scriptName = trim($_SERVER['SCRIPT_NAME'] ?? '', '/');
    $segments = $scriptName !== '' ? explode('/', $scriptName) : [];
    $basePath = isset($segments[0]) && $segments[0] !== '' ? '/' . $segments[0] : '';

    $isActive = static function (string $path) use ($currentPath): string {
        return str_contains($currentPath, $path) ? 'active' : '';
    };
    ?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --brand: #be123c;
            --brand-dark: #881337;
            --accent: #fb7185;
            --accent-soft: rgba(251, 113, 133, 0.16);
            --surface: rgba(255,255,255,0.92);
            --surface-soft: rgba(255,255,255,0.75);
            --surface-elevated: #ffffff;
            --text-main: #1f1720;
            --text-muted: #6b5560;
            --border-soft: rgba(159, 18, 57, 0.14);
            --shadow-soft: 0 10px 30px rgba(80, 10, 28, 0.08);
            --shadow-card: 0 18px 45px rgba(80, 10, 28, 0.16);
            --radius-lg: 1.25rem;
            --radius-xl: 1.75rem;
            --body-bg:
                radial-gradient(circle at top left, rgba(225,29,72,0.10), transparent 28%),
                radial-gradient(circle at top right, rgba(159,18,57,0.12), transparent 24%),
                linear-gradient(180deg, #fff7f8 0%, #fff1f4 100%);
            --hero-card-text: #1f1720;
            --hero-card-muted: #6b5560;
            --table-head-bg: rgba(159, 18, 57, 0.08);
            --table-row-border: rgba(159, 18, 57, 0.08);
            --input-bg: #ffffff;
            --input-border: rgba(15, 23, 42, 0.12);
            --empty-bg: rgba(255,255,255,0.6);
            --empty-border: rgba(15, 23, 42, 0.14);
            --footer-bg: #4a0718;
            --footer-text: rgba(255,255,255,0.82);
            --navbar-bg: rgba(74, 7, 24, 0.94);
            --outline-btn-bg: rgba(255,255,255,0.75);
        }

        body.theme-dark {
            --brand: #e11d48;
            --brand-dark: #be123c;
            --accent: #fb7185;
            --accent-soft: rgba(251, 113, 133, 0.18);
            --surface: rgba(19, 19, 26, 0.88);
            --surface-soft: rgba(24, 24, 34, 0.82);
            --surface-elevated: #181822;
            --text-main: #f8dfe5;
            --text-muted: #c8a7b0;
            --border-soft: rgba(251, 113, 133, 0.16);
            --shadow-soft: 0 14px 34px rgba(0, 0, 0, 0.34);
            --shadow-card: 0 24px 56px rgba(0, 0, 0, 0.42);
            --body-bg:
                radial-gradient(circle at top left, rgba(225,29,72,0.16), transparent 22%),
                radial-gradient(circle at top right, rgba(127,29,29,0.22), transparent 18%),
                linear-gradient(180deg, #0d0b10 0%, #120d14 45%, #170d12 100%);
            --hero-card-text: #f9e7ec;
            --hero-card-muted: #d8b7bf;
            --table-head-bg: rgba(225, 29, 72, 0.12);
            --table-row-border: rgba(255,255,255,0.06);
            --input-bg: rgba(17, 17, 26, 0.94);
            --input-border: rgba(255,255,255,0.08);
            --empty-bg: rgba(24, 24, 34, 0.75);
            --empty-border: rgba(255,255,255,0.08);
            --footer-bg: #0f0a10;
            --footer-text: rgba(255,255,255,0.76);
            --navbar-bg: rgba(11, 8, 12, 0.9);
            --outline-btn-bg: rgba(255,255,255,0.02);
        }

        html { scroll-behavior: smooth; }
        body {
            background: var(--body-bg);
            color: var(--text-main);
            transition: background .25s ease, color .25s ease;
        }

        .navbar-glass {
            background: var(--navbar-bg) !important;
            backdrop-filter: blur(12px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.18);
        }

        .navbar-brand {
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .nav-link {
            border-radius: 999px;
            padding: 0.55rem 0.95rem !important;
            color: rgba(255,255,255,0.82) !important;
        }

        .nav-link.active,
        .nav-link:hover {
            background: rgba(255,255,255,0.12);
            color: #fff !important;
        }

        .page-shell {
            width: min(1180px, calc(100% - 1.5rem));
            margin-inline: auto;
        }

        .section-card,
        .soft-card {
            border: 1px solid var(--border-soft);
            background: var(--surface);
            backdrop-filter: blur(8px);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-soft);
            color: var(--text-main);
        }

        .soft-card {
            border-radius: var(--radius-lg);
            background: var(--surface-soft);
        }

        .hero-surface {
            position: relative;
            overflow: hidden;
            border-radius: 2rem;
            padding: 3rem;
            background:
                linear-gradient(135deg, rgba(69, 10, 24, 0.98), rgba(136, 19, 55, 0.97), rgba(190, 24, 93, 0.94)),
                var(--brand);
            color: #fff;
            box-shadow: var(--shadow-card);
        }

        .hero-surface::after {
            content: "";
            position: absolute;
            inset: auto -60px -80px auto;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,0.10);
        }

        .hero-title {
            font-size: clamp(2rem, 4vw, 3.35rem);
            font-weight: 800;
            line-height: 1.05;
            letter-spacing: -0.04em;
        }

        .hero-subtitle {
            font-size: 1.05rem;
            color: rgba(255,255,255,0.86);
            max-width: 60ch;
        }

        .stat-chip {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            padding: .7rem 1rem;
            border-radius: 999px;
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.14);
            color: #fff;
            font-weight: 600;
        }

        .feature-icon,
        .info-icon {
            width: 3rem;
            height: 3rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 1rem;
            background: linear-gradient(135deg, rgba(225,29,72,0.16), rgba(251,113,133,0.12));
            color: #ffd9e1;
            font-size: 1.2rem;
            border: 1px solid rgba(251,113,133,0.18);
        }

        .section-card .feature-icon,
        .soft-card .info-icon,
        .soft-card .feature-icon,
        .location-card .feature-icon,
        .app-card .feature-icon {
            color: var(--brand);
            background: linear-gradient(135deg, rgba(225,29,72,0.14), rgba(251,113,133,0.10));
        }

        .location-card,
        .app-card {
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-xl);
            overflow: hidden;
            background: var(--surface);
            box-shadow: var(--shadow-soft);
            transition: transform .22s ease, box-shadow .22s ease;
            color: var(--text-main);
        }

        .location-card:hover,
        .app-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-card);
        }

        .location-img,
        .card-cover {
            height: 250px;
            width: 100%;
            object-fit: cover;
            background: linear-gradient(135deg, #1f1520, #2b1720);
        }

        .calendar-day {
            border-radius: 1.1rem;
            padding: 1rem;
            font-weight: 600;
            min-height: 150px;
            border: 1px solid var(--border-soft);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform .2s ease, box-shadow .2s ease;
            box-shadow: var(--shadow-soft);
        }

        .calendar-day.free:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 28px rgba(25, 135, 84, 0.22);
        }

        .calendar-day.busy {
            opacity: .95;
        }

        .table-shell {
            border-radius: var(--radius-xl);
            overflow: hidden;
            border: 1px solid var(--border-soft);
            background: var(--surface-elevated);
            box-shadow: var(--shadow-soft);
        }

        .table {
            --bs-table-bg: transparent;
            --bs-table-color: var(--text-main);
            --bs-table-border-color: var(--table-row-border);
            margin-bottom: 0;
        }

        .table thead th {
            border-bottom: 0;
            font-size: .9rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            background: var(--table-head-bg);
            color: var(--text-main);
        }

        .table > :not(caption) > * > * {
            padding: 1rem 1rem;
            vertical-align: middle;
        }

        .auth-card {
            max-width: 560px;
            margin: 0 auto;
            border-radius: 1.75rem;
            border: 1px solid var(--border-soft);
            background: var(--surface);
            box-shadow: var(--shadow-card);
        }

        .form-control,
        .form-select {
            border-radius: .95rem;
            padding: .85rem 1rem;
            border-color: var(--input-border);
            background: var(--input-bg);
            color: var(--text-main);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 .25rem rgba(225,29,72,0.18);
            border-color: rgba(251,113,133,0.42);
            background: var(--input-bg);
            color: var(--text-main);
        }

        .btn {
            border-radius: .95rem;
            padding: .8rem 1.1rem;
            font-weight: 600;
        }

        .btn-pill {
            border-radius: 999px;
            padding-inline: 1.15rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--brand), var(--accent));
            border-color: var(--brand);
            box-shadow: 0 10px 22px rgba(159,18,57,0.22);
        }

        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active {
            background: linear-gradient(135deg, var(--brand-dark), var(--brand));
            border-color: var(--brand-dark);
        }

        .btn-outline-primary {
            color: var(--brand);
            border-color: rgba(251,113,133,0.35);
            background: var(--outline-btn-bg);
        }

        .btn-outline-primary:hover,
        .btn-outline-primary:focus,
        .btn-outline-primary:active {
            color: #fff;
            background: var(--brand);
            border-color: var(--brand);
        }

        .btn-outline-dark {
            color: var(--text-main);
            border-color: var(--input-border);
            background: transparent;
        }

        .btn-outline-dark:hover,
        .btn-outline-dark:focus,
        .btn-outline-dark:active {
            color: #fff;
            background: var(--brand);
            border-color: var(--brand);
        }

        .btn-warning {
            background: #fbbf24;
            border-color: #f59e0b;
            color: #3b2000;
        }

        .btn-warning:hover,
        .btn-warning:focus,
        .btn-warning:active {
            background: #f59e0b;
            border-color: #d97706;
            color: #2a1700;
        }

        .text-bg-primary,
        .bg-primary {
            background-color: var(--brand) !important;
        }

        .text-primary,
        .link-primary,
        .link-primary:hover,
        a {
            color: var(--brand);
        }

        a:hover {
            color: var(--accent);
        }

        .text-muted,
        .section-subtitle,
        .form-text,
        .small,
        small {
            color: var(--text-muted) !important;
        }

        .text-dark,
        body.theme-dark .text-dark {
            color: var(--hero-card-text) !important;
        }

        .pagination .page-link {
            color: var(--brand);
            background: var(--surface-elevated);
            border-color: var(--border-soft);
        }

        .pagination .active > .page-link,
        .page-link.active {
            background-color: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }

        .section-title {
            font-size: clamp(1.5rem, 3vw, 2rem);
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .badge-soft {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            background: var(--accent-soft);
            color: var(--brand);
            padding: .55rem .8rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: .84rem;
            border: 1px solid rgba(251,113,133,0.10);
        }

        .empty-state {
            padding: 2.5rem 1.5rem;
            border: 1px dashed var(--empty-border);
            border-radius: var(--radius-lg);
            background: var(--empty-bg);
            text-align: center;
            color: var(--text-muted);
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.14);
            color: #c8ffe9;
        }

        .footer-dark {
            background: var(--footer-bg);
            color: var(--footer-text);
        }

        @media (max-width: 768px) {
            .hero-surface { padding: 2rem; }
        }
    </style>
</head>
<body class="theme-dark">
<div class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark navbar-glass sticky-top border-bottom border-white border-opacity-10">
        <div class="container page-shell py-2">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= $basePath ?>/index.php">
                <span class="feature-icon text-white bg-white bg-opacity-10 border border-white border-opacity-10"><i class="bi bi-calendar2-check"></i></span>
                <span>Evenimente Sibiu</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-1 mt-3 mt-lg-0">
                    <li class="nav-item"><a class="nav-link <?= $isActive('/index.php') ?>" href="<?= $basePath ?>/index.php">Acasă</a></li>
                    <li class="nav-item"><a class="nav-link <?= $isActive('/locatii.php') ?>" href="<?= $basePath ?>/locatii.php">Locații</a></li>
                    <li class="nav-item"><a class="nav-link <?= $isActive('/evenimente.php') ?>" href="<?= $basePath ?>/evenimente.php">Evenimente</a></li>
                    <?php if ($isAdmin): ?>
                        <li class="nav-item"><a class="nav-link <?= $isActive('/admin/') ?>" href="<?= $basePath ?>/admin/index.php">Admin</a></li>
                    <?php endif; ?>
                </ul>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button class="btn btn-outline-light btn-sm btn-pill" type="button" id="themeToggle" aria-label="Comută tema">
                        <i class="bi bi-moon-stars-fill me-1"></i><span id="themeToggleText">Dark</span>
                    </button>
                    <?php if ($loggedIn): ?>
                        <span class="badge-soft bg-white bg-opacity-10 text-white border border-white border-opacity-10">
                            <i class="bi bi-person-circle"></i> <?= $nume ?>
                        </span>
                        <a href="<?= $basePath ?>/logout.php" class="btn btn-outline-light btn-sm btn-pill">Logout</a>
                    <?php else: ?>
                        <a href="<?= $basePath ?>/login.php" class="btn btn-outline-light btn-sm btn-pill">Login</a>
                        <a href="<?= $basePath ?>/register.php" class="btn btn-warning btn-sm btn-pill">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="page-shell py-4 py-lg-5 flex-grow-1">
<?php
}

function render_footer()
{
?>
    </main>

    <footer class="footer-dark mt-auto border-top border-white border-opacity-10">
        <div class="page-shell py-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div>
                <strong>Evenimente Sibiu</strong><br>
                <small>Sistem de rezervări pentru locații și evenimente.</small>
            </div>
            <small>&copy; <?= date('Y') ?> Toate drepturile rezervate.</small>
        </div>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        const storageKey = 'rezervari2-theme';
        const body = document.body;
        const toggle = document.getElementById('themeToggle');
        const label = document.getElementById('themeToggleText');

        function applyTheme(theme) {
            const isDark = theme === 'dark';
            body.classList.toggle('theme-dark', isDark);
            body.classList.toggle('theme-light', !isDark);
            if (label) {
                label.textContent = isDark ? 'Dark' : 'Light';
            }
            if (toggle) {
                toggle.innerHTML = isDark
                    ? '<i class="bi bi-moon-stars-fill me-1"></i><span id="themeToggleText">Dark</span>'
                    : '<i class="bi bi-sun-fill me-1"></i><span id="themeToggleText">Light</span>';
            }
        }

        const saved = localStorage.getItem(storageKey);
        const preferredDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        applyTheme(saved || (preferredDark ? 'dark' : 'light'));

        document.addEventListener('click', function (event) {
            const btn = event.target.closest('#themeToggle');
            if (!btn) return;
            const nextTheme = body.classList.contains('theme-dark') ? 'light' : 'dark';
            localStorage.setItem(storageKey, nextTheme);
            applyTheme(nextTheme);
        });
    })();
</script>
</body>
</html>
<?php
}
