<?php
// ==========================================
// 1. DYNAMIC BASE URL
// ==========================================
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$domain   = $_SERVER['HTTP_HOST'];
$folder   = dirname($_SERVER['SCRIPT_NAME']);
$folder   = ($folder === '/' || $folder === '\\') ? '' : $folder;

if (!defined('BASE_URL')) {
    define('BASE_URL', $protocol . '://' . $domain . $folder);
}

// ==========================================
// 2. CORE ROUTING (INTERCEPT API & CLIENT)
// ==========================================
$url = isset($_GET['url']) && !empty($_GET['url']) ? rtrim($_GET['url'], '/') : 'home';

// Fallback aaPanel/nginx
if ($url === 'home' && isset($_GET['s']) && !empty($_GET['s'])) {
    $url = rtrim($_GET['s'], '/');
}

$url      = filter_var($url, FILTER_SANITIZE_URL);
$urlParts = explode('/', ltrim($url, '/'));

// --- Route: /client/* -> panel admin ---
if ($urlParts[0] === 'client') {
    $_GET['page'] = !empty($urlParts[1]) ? $urlParts[1] : 'dashboard';
    if (isset($urlParts[2])) $_GET['id'] = $urlParts[2];
    require __DIR__ . '/client/index.php';
    exit;
}

// --- Route: /api/* -> REST API ---
if ($urlParts[0] === 'api') {
    $_GET['request'] = implode('/', array_slice($urlParts, 1));
    require __DIR__ . '/api/index.php';
    exit;
}

// ==========================================
// 3. PUBLIC PAGES
// ==========================================
include __DIR__ . '/views/header.php';
include __DIR__ . '/views/script.php';   // APP global HARUS sebelum pages yg pakai APP.api()
include __DIR__ . '/views/navbar.php';

$page  = basename($urlParts[0] ?? 'home');
$param = $urlParts[1] ?? null;

// Whitelist halaman publik
$allowed = ['home', 'lowongan', 'detail', 'form', 'status', 'faq'];
if (!in_array($page, $allowed, true)) {
    $page = '404';
}

// inject id param
if ($param !== null) {
    $_GET['id'] = htmlspecialchars($param);
}

$path = __DIR__ . "/pages/{$page}.php";

if (file_exists($path)) {
    include $path;
} else {
    // 404
    echo '
    <div class="container mx-auto px-6 py-20 mt-10 text-center min-h-[60vh] flex flex-col justify-center items-center">
        <h1 class="text-9xl font-extrabold text-indigo-900">404</h1>
        <div class="bg-orange-500 text-white px-2 text-sm rounded rotate-12 absolute">Halaman Tidak Ditemukan</div>
        <p class="text-gray-600 mt-8 mb-6 text-lg">Halaman yang kamu cari belum tersedia atau sudah dipindah.</p>
        <a href="' . BASE_URL . '/home" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl transition duration-300 shadow-lg">
            Kembali ke Beranda
        </a>
    </div>';
}

include __DIR__ . '/views/footer.php';
