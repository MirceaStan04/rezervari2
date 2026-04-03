<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Europe/Bucharest');

$host = getenv('PGHOST') ?: 'localhost';
$port = getenv('PGPORT') ?: '5432';
$dbname = getenv('PGDATABASE') ?: 'licenta';
$user = getenv('PGUSER') ?: 'postgres';
$password = getenv('PGPASSWORD') ?: '1770';

try {
    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec('SET search_path TO public;');
} catch (PDOException $e) {
    die('Eroare la conexiunea PostgreSQL: ' . htmlspecialchars($e->getMessage()));
}

$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$projectRoot = preg_replace('#/admin$#', '', $scriptDir);
if ($projectRoot === '.' || $projectRoot === '/') {
    $projectRoot = '';
}
define('APP_BASE_PATH', $projectRoot);

auto_store_last_page();

function auto_store_last_page(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
        return;
    }

    $uri = $_SERVER['REQUEST_URI'] ?? '';
    if ($uri === '') {
        return;
    }

    $path = parse_url($uri, PHP_URL_PATH) ?: '';
    if (preg_match('#/(login|logout|register|gen_hash|test_conexiune)\.php$#', $path)) {
        return;
    }

    $_SESSION['last_page'] = $uri;
}

function url(string $path = ''): string
{
    $path = ltrim($path, '/');
    return APP_BASE_PATH . ($path !== '' ? '/' . $path : '');
}

function asset(string $path): string
{
    return url($path);
}

function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function is_admin(): bool
{
    return is_logged_in() && (($_SESSION['rol'] ?? '') === 'admin');
}

function require_login(): void
{
    if (!is_logged_in()) {
        set_flash('warning', 'Trebuie sa fii autentificat pentru a continua.');
        header('Location: ' . url('login.php'));
        exit;
    }
}

function loc_image(?string $path): string
{
    $path = trim((string)$path);
    if ($path === '') {
        return asset('img/sala-thalia.jpg');
    }

    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    if (str_starts_with($path, '/')) {
        return $path;
    }

    return url($path);
}

function format_date_ro(?string $date): string
{
    if (!$date) {
        return '-';
    }

    $ts = strtotime($date);
    return $ts ? date('d.m.Y', $ts) : $date;
}

function format_money($value): string
{
    return number_format((float)$value, 2, ',', '.') . ' lei';
}


function short_text(?string $text, int $limit = 120): string
{
    $text = trim((string)$text);
    if ($text === '') {
        return '';
    }

    if (function_exists('mb_strimwidth')) {
        return mb_strimwidth($text, 0, $limit, '...');
    }

    return strlen($text) > $limit ? substr($text, 0, max(0, $limit - 3)) . '...' : $text;
}

function normalize_status(?string $status): string
{
    $status = strtolower(trim((string)$status));
    $map = [
        'in asteptare' => 'in_asteptare',
        'in_asteptare' => 'in_asteptare',
        'asteptare' => 'in_asteptare',
        'pending' => 'in_asteptare',
        'noua' => 'in_asteptare',
        'aprobata' => 'aprobata',
        'confirmata' => 'aprobata',
        'acceptata' => 'aprobata',
        'approved' => 'aprobata',
        'activa' => 'aprobata',
        'respinsa' => 'respinsa',
        'anulata' => 'respinsa',
        'rejected' => 'respinsa',
        'cancelled' => 'respinsa',
    ];

    return $map[$status] ?? ($status !== '' ? $status : 'necunoscut');
}

function status_label(?string $status): string
{
    return match (normalize_status($status)) {
        'in_asteptare' => 'In asteptare',
        'aprobata' => 'Aprobata',
        'respinsa' => 'Respinsa',
        default => ucfirst(str_replace('_', ' ', (string)$status)),
    };
}

function status_badge_class(?string $status): string
{
    return match (normalize_status($status)) {
        'in_asteptare' => 'badge-soft warning',
        'aprobata' => 'badge-soft success',
        'respinsa' => 'badge-soft danger',
        default => 'badge-soft neutral',
    };
}

function card_stat(PDO $pdo, string $table): int
{
    return (int)$pdo->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
}

function insert_user_compatible(PDO $pdo, string $nume, string $email, string $hash): void
{
    $last = null;
    $roleCandidates = ['user', 'utilizator', 'client'];

    $roleQueries = [
        'INSERT INTO utilizatori (nume, email, parola, rol, data_creare) VALUES (?, ?, ?, ?, CURRENT_DATE)',
        'INSERT INTO utilizatori (nume, email, parola, rol, data_creare) VALUES (?, ?, ?, ?, NOW())',
        'INSERT INTO utilizatori (nume, email, parola, rol) VALUES (?, ?, ?, ?)',
    ];

    foreach ($roleQueries as $sql) {
        foreach ($roleCandidates as $role) {
            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nume, $email, $hash, $role]);
                return;
            } catch (PDOException $e) {
                $last = $e;
            }
        }
    }

    $simpleQueries = [
        'INSERT INTO utilizatori (nume, email, parola, data_creare) VALUES (?, ?, ?, CURRENT_DATE)',
        'INSERT INTO utilizatori (nume, email, parola, data_creare) VALUES (?, ?, ?, NOW())',
        'INSERT INTO utilizatori (nume, email, parola) VALUES (?, ?, ?)',
    ];

    foreach ($simpleQueries as $sql) {
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nume, $email, $hash]);
            return;
        } catch (PDOException $e) {
            $last = $e;
        }
    }

    if ($last) {
        throw $last;
    }
}

function insert_rezervare_compatible(PDO $pdo, int $idUtilizator, int $idEveniment, int $numarLocuri): void
{
    $last = null;
    $statusCandidates = ['in_asteptare', 'asteptare', 'pending', 'noua', 'activa', 'aprobata'];
    $queries = [
        'INSERT INTO rezervari (id_utilizator, id_eveniment, numar_locuri, data_rezervare, status) VALUES (?, ?, ?, CURRENT_DATE, ?)',
        'INSERT INTO rezervari (id_utilizator, id_eveniment, numar_locuri, data_rezervare, status) VALUES (?, ?, ?, NOW(), ?)',
        'INSERT INTO rezervari (id_utilizator, id_eveniment, numar_locuri, status) VALUES (?, ?, ?, ?)',
    ];

    foreach ($queries as $sql) {
        foreach ($statusCandidates as $status) {
            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$idUtilizator, $idEveniment, $numarLocuri, $status]);
                return;
            } catch (PDOException $e) {
                $last = $e;
            }
        }
    }

    if ($last) {
        throw $last;
    }
}

function update_rezervare_status_compatible(PDO $pdo, int $idRezervare, string $action): bool
{
    $candidates = $action === 'approve'
        ? ['aprobata', 'confirmata', 'acceptata', 'approved', 'activa']
        : ['respinsa', 'anulata', 'rejected', 'cancelled'];

    foreach ($candidates as $status) {
        try {
            $stmt = $pdo->prepare('UPDATE rezervari SET status = ? WHERE id_rezervare = ?');
            $stmt->execute([$status, $idRezervare]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            continue;
        }
    }

    return false;
}
