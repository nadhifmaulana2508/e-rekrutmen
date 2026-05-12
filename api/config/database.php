<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers/response.php';

/**
 * Cari project root dengan mendeteksi file .env ke atas max 6 level.
 * Default fallback: 2 level di atas (api/config -> project root).
 */
function findProjectRoot(string $startDir): string {
    $dir = $startDir;
    for ($i = 0; $i < 6; $i++) {
        if (is_file($dir . '/.env')) return $dir;
        $parent = dirname($dir);
        if ($parent === $dir) break;
        $dir = $parent;
    }
    return dirname($startDir, 2);
}

$PROJECT_ROOT = findProjectRoot(__DIR__);
$envFile      = $PROJECT_ROOT . '/.env';

/**
 * Load .env sebagai array. Jika file tidak ada, gunakan default (local XAMPP).
 * Error parsing -> balas JSON error 500.
 */
function loadEnv(string $file): array {
    if (!is_file($file)) {
        // Default local agar dev cepat start tanpa perlu .env
        return [
            'DB_HOST' => 'localhost',
            'DB_PORT' => '3306',
            'DB_USER' => 'root',
            'DB_PASS' => '',
            'DB_NAME' => 'rekrutmen_db',
            'JWT_SECRET' => 'dev-secret-change-me',
        ];
    }
    $data = parse_ini_file($file, false, INI_SCANNER_RAW);
    if ($data === false) {
        sendResponse(500, "Gagal membaca .env di: {$file}");
        exit;
    }
    return $data;
}

$env = loadEnv($envFile);

$ENV = function (string $key, $default = null) use ($env) {
    $v = getenv($key);
    if ($v !== false && $v !== '') return $v;
    return $env[$key] ?? $default;
};

$DB_HOST = (string)$ENV('DB_HOST', 'localhost');
$DB_USER = (string)$ENV('DB_USER', 'root');
$DB_PASS = (string)$ENV('DB_PASS', '');
$DB_NAME = (string)$ENV('DB_NAME', 'rekrutmen_db');
$DB_PORT = (int)$ENV('DB_PORT', 3306);

// Expose JWT secret sebagai konstanta global
if (!defined('JWT_SECRET')) {
    define('JWT_SECRET', (string)$ENV('JWT_SECRET', 'dev-secret-change-me'));
}

try {
    $dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    sendResponse(500, "Koneksi database gagal: " . $e->getMessage());
    exit;
}
