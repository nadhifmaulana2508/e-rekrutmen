<?php

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/JWT.php';

/**
 * Pastikan request membawa Bearer token valid (SSO token).
 * Verifikasi via SSO whoami endpoint.
 * Return payload user dari token jika OK.
 */
function requireAuth(): array {
    $headers = function_exists('getallheaders') ? getallheaders() : [];

    // Normalisasi key (sebagian server lowercase)
    $normalized = [];
    foreach ($headers as $k => $v) $normalized[strtolower($k)] = $v;

    $token = $normalized['authorization'] ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? '');

    if (stripos($token, 'Bearer ') === 0) {
        $token = substr($token, 7);
    } elseif (!empty($_COOKIE['sso_token'])) {
        // Fallback: ambil dari cookie
        $token = $_COOKIE['sso_token'];
    } else {
        sendResponse(401, 'Token tidak ditemukan');
    }

    if (empty($token)) {
        sendResponse(401, 'Token tidak ditemukan');
    }

    // Coba verifikasi sebagai JWT lokal dulu (backward compat)
    $user = verifyJWT($token);
    if ($user) {
        return $user;
    }

    // Jika bukan JWT lokal, verifikasi via SSO whoami
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $is_localhost = in_array($host, ['localhost', '127.0.0.1'], true)
                || str_starts_with($host, 'localhost:')
                || str_starts_with($host, '127.0.0.1:');

    $ssoUrl = $is_localhost
        ? 'http://localhost/rest_api_sso/api/auth/whoami'
        : 'https://apisso.bkkjateng.co.id/api/auth/whoami';

    $ch = curl_init($ssoUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || $response === false) {
        sendResponse(401, 'Token tidak valid atau kadaluarsa');
    }

    $result = json_decode($response, true);
    if (empty($result['data'])) {
        sendResponse(401, 'Token tidak valid atau kadaluarsa');
    }

    // Return user data dari SSO
    $userData = $result['data'];
    return [
        'id'         => $userData['id'] ?? $userData['id_peg'] ?? 0,
        'id_peg'     => $userData['employee_id'] ?? $userData['id_peg'] ?? $userData['kode'] ?? '',
        'username'   => $userData['username'] ?? $userData['employee_id'] ?? '',
        'nama'       => $userData['full_name'] ?? $userData['nama'] ?? '',
        'role'       => $userData['role'] ?? 'admin',
        'unit_kerja' => $userData['unit_kerja'] ?? $userData['job_position'] ?? '',
    ];
}
