<?php
// Session-based admin login yang menyimpan token JWT dari API.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error_login = null;
$is_logged_in = false;
$admin_user   = null;

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . BASE_URL . '/client/login');
    exit;
}

// Handle login POST
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $apiUrl = BASE_URL . '/api/auth/login';
    $payload = json_encode([
        'username' => trim($_POST['username']),
        'password' => $_POST['password'],
    ]);

    // Pola dari PR #8 project lelang: hindari loopback HTTP jika bisa pakai PDO langsung.
    // Untuk login, kita panggil controller langsung (bukan HTTP loopback).
    require_once __DIR__ . '/../../api/config/database.php';
    require_once __DIR__ . '/../../api/controllers/AuthController.php';

    // Capture output JSON dari controller
    ob_start();
    try {
        $controller = new AuthController($pdo);
        $controller->login([
            'username' => trim($_POST['username']),
            'password' => $_POST['password'],
        ]);
    } catch (Throwable $e) {
        // sendResponse() exit, jadi jarang sampai sini
    }
    $json = ob_get_clean();
    $res  = json_decode($json, true);

    if (is_array($res) && ($res['status'] ?? 0) === 200 && !empty($res['data']['token'])) {
        $_SESSION['token'] = $res['data']['token'];
        $_SESSION['user']  = $res['data']['user'];
        header('Location: ' . BASE_URL . '/client/dashboard');
        exit;
    } else {
        $error_login = $res['message'] ?? 'Login gagal';
    }
}

// Check session
if (!empty($_SESSION['token']) && !empty($_SESSION['user'])) {
    $is_logged_in = true;
    $admin_user   = $_SESSION['user'];
}
