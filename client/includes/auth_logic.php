<?php
/**
 * Auth logic admin panel - Cookie-based SSO (seperti monbis).
 * 
 * Login dilakukan di frontend via JS fetch ke SSO API.
 * Backend hanya cek apakah cookie `sso_token` ada.
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

// Handle logout (hapus cookie + localStorage di FE)
if (isset($_GET['logout'])) {
    // Hapus cookie sso_token
    setcookie('sso_token', '', time() - 3600, '/');
    // Hapus cookie domain (untuk produksi)
    setcookie('sso_token', '', time() - 3600, '/', '.bkkjateng.co.id');
    session_destroy();
    header('Location: ' . BASE_URL . '/client/login');
    exit;
}

// CEK STATUS LOGIN VIA COOKIE sso_token
$is_logged_in = isset($_COOKIE['sso_token']) && !empty($_COOKIE['sso_token']);

// Ambil user data dari localStorage (dikirim via JS ke window object)
// Di PHP kita hanya perlu tahu apakah cookie ada untuk routing guard
$admin_user = [
    'id'         => 0,
    'id_peg'     => '',
    'username'   => '',
    'nama'       => 'User',
    'email'      => '',
    'role'       => 'admin',
    'unit_kerja' => '',
];

// Jika ada session user (fallback), gunakan itu
if (!empty($_SESSION['user'])) {
    $admin_user = $_SESSION['user'];
}
