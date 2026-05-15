<?php

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/JWT.php';

/**
 * Pastikan request membawa Bearer token valid.
 * 1. Coba verifikasi sebagai JWT lokal
 * 2. Fallback: verifikasi via SSO whoami
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
        $token = $_COOKIE['sso_token'];
    } else {
        sendResponse(401, 'Token tidak ditemukan');
    }

    if (empty($token)) {
        sendResponse(401, 'Token tidak ditemukan');
    }

    // === STEP 1: Coba verifikasi sebagai JWT lokal ===
    $user = verifyJWT($token);
    if ($user) {
        return $user;
    }

    // === STEP 2: Verifikasi via SSO whoami ===
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
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false || $curlError) {
        sendResponse(401, 'Gagal verifikasi token: ' . ($curlError ?: 'SSO tidak merespons'));
    }

    if ($httpCode !== 200) {
        sendResponse(401, 'Token tidak valid atau kadaluarsa');
    }

    $result = json_decode($response, true);

    // Support berbagai format response SSO:
    // Format 1: {"status": 200, "data": {...}}
    // Format 2: {"data": {...}}
    // Format 3: {"user": {...}}
    // Format 4: langsung flat object {"id": ..., "nama": ...}
    $userData = null;

    if (isset($result['data']) && is_array($result['data'])) {
        $userData = $result['data'];
    } elseif (isset($result['user']) && is_array($result['user'])) {
        $userData = $result['user'];
    } elseif (isset($result['status']) && $result['status'] === 200 && isset($result['data'])) {
        $userData = $result['data'];
    } elseif (isset($result['id']) || isset($result['employee_id']) || isset($result['nama'])) {
        // Flat object response
        $userData = $result;
    }

    if (!$userData) {
        sendResponse(401, 'Token tidak valid atau kadaluarsa');
    }

    // Determine role
    $unitKerja = $userData['unit_kerja'] ?? $userData['job_position'] ?? $userData['divisi'] ?? '';
    $unitLower = strtolower(trim($unitKerja));
    $role = in_array($unitLower, ['divisi operasional', 'divisi sdm dan umum'], true) ? 'superadmin' : 'admin';

    return [
        'id'         => $userData['id'] ?? $userData['id_peg'] ?? $userData['employee_id'] ?? 0,
        'id_peg'     => $userData['employee_id'] ?? $userData['id_peg'] ?? $userData['kode'] ?? '',
        'username'   => $userData['username'] ?? $userData['account_handle'] ?? $userData['employee_id'] ?? '',
        'nama'       => $userData['full_name'] ?? $userData['nama'] ?? $userData['nama_lengkap'] ?? '',
        'email'      => $userData['email'] ?? '',
        'role'       => $role,
        'unit_kerja' => $unitKerja,
    ];
}
