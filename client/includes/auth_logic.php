<?php
/**
 * Auth logic admin panel - Cookie-based SSO (seperti monbis).
 * 
 * Login dilakukan di frontend via JS fetch ke SSO API.
 * Backend cek cookie sso_token ATAU query param token.
 * 
 * Role superadmin jika unit_kerja (lowercase):
 * - "divisi operasional"
 * - "divisi sdm dan umum"
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error_login  = null;
$is_logged_in = false;
$admin_user   = null;

// Handle logout (hapus cookie + session di server)
if (isset($_GET['logout'])) {
    // Hapus cookie sso_token (semua variasi)
    setcookie('sso_token', '', time() - 3600, '/');
    setcookie('sso_token', '', time() - 3600, '/', '.bkkjateng.co.id');
    // Hapus session
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy();
    header('Location: ' . BASE_URL . '/client/login');
    exit;
}

// CEK STATUS LOGIN:
// 1. Cookie sso_token
// 2. Session token (fallback)
$sso_token = $_COOKIE['sso_token'] ?? '';

// Jika cookie kosong, coba dari session
if (empty($sso_token) && !empty($_SESSION['sso_token'])) {
    $sso_token = $_SESSION['sso_token'];
}

$is_logged_in = !empty($sso_token);

// Ambil user data default
$admin_user = [
    'id'         => 0,
    'id_peg'     => '',
    'username'   => '',
    'nama'       => 'User',
    'email'      => '',
    'role'       => 'admin',
    'unit_kerja' => '',
];

// Jika ada session user, gunakan itu
if (!empty($_SESSION['user'])) {
    $admin_user = $_SESSION['user'];
}
