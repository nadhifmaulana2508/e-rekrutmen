<?php
/**
 * POST /api/session/set-token
 * Simpan SSO token ke PHP session (fallback jika cookie gagal).
 * Dipanggil oleh FE setelah login berhasil.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 405, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true) ?? [];
$token = $data['token'] ?? '';
$user  = $data['user'] ?? null;

if (empty($token)) {
    echo json_encode(['status' => 400, 'message' => 'Token wajib']);
    exit;
}

$_SESSION['sso_token'] = $token;
if ($user && is_array($user)) {
    $_SESSION['user'] = $user;
}

echo json_encode(['status' => 200, 'message' => 'Session saved']);
