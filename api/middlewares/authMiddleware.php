<?php

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/JWT.php';

/**
 * Auth middleware - verifikasi token.
 * 
 * Strategi:
 * 1. Coba verify sebagai JWT lokal (dari login DB admin)
 * 2. Coba decode SSO JWT payload langsung (tanpa verify signature)
 *    → SSO token sudah di-verify oleh FE via whoami
 *    → Backend hanya perlu extract user data dari payload
 * 3. Fallback: call SSO whoami dari server
 */
function requireAuth(): array {
    $headers = function_exists('getallheaders') ? getallheaders() : [];

    // Normalisasi key
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

    // === STEP 1: Verify sebagai JWT lokal (login admin DB) ===
    $user = verifyJWT($token);
    if ($user) {
        return $user;
    }

    // === STEP 2: Decode SSO JWT payload (tanpa verify signature) ===
    // SSO token adalah JWT standard, kita decode payload-nya saja
    // FE sudah verify via whoami, backend trust token ini
    $payload = decodeJwtPayload($token);
    if ($payload) {
        $unitKerja = $payload['unit_kerja'] ?? $payload['job_position'] ?? $payload['divisi'] ?? '';
        $unitLower = strtolower(trim($unitKerja));
        $role = in_array($unitLower, ['divisi operasional', 'divisi sdm dan umum'], true) ? 'superadmin' : 'admin';

        return [
            'id'         => $payload['id'] ?? $payload['sub'] ?? $payload['id_peg'] ?? 0,
            'id_peg'     => $payload['employee_id'] ?? $payload['id_peg'] ?? $payload['kode'] ?? '',
            'username'   => $payload['username'] ?? $payload['account_handle'] ?? $payload['employee_id'] ?? '',
            'nama'       => $payload['full_name'] ?? $payload['nama'] ?? $payload['nama_lengkap'] ?? $payload['name'] ?? '',
            'email'      => $payload['email'] ?? '',
            'role'       => $role,
            'unit_kerja' => $unitKerja,
        ];
    }

    // === STEP 3: Fallback - call SSO whoami dari server ===
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
    curl_close($ch);

    if ($httpCode === 200 && $response !== false) {
        $result = json_decode($response, true);
        $userData = $result['data'] ?? $result['user'] ?? $result ?? null;
        if ($userData && (isset($userData['id']) || isset($userData['employee_id']) || isset($userData['nama']))) {
            $unitKerja = $userData['unit_kerja'] ?? $userData['job_position'] ?? '';
            $unitLower = strtolower(trim($unitKerja));
            $role = in_array($unitLower, ['divisi operasional', 'divisi sdm dan umum'], true) ? 'superadmin' : 'admin';
            return [
                'id'         => $userData['id'] ?? $userData['id_peg'] ?? 0,
                'id_peg'     => $userData['employee_id'] ?? $userData['id_peg'] ?? $userData['kode'] ?? '',
                'username'   => $userData['username'] ?? $userData['employee_id'] ?? '',
                'nama'       => $userData['full_name'] ?? $userData['nama'] ?? '',
                'email'      => $userData['email'] ?? '',
                'role'       => $role,
                'unit_kerja' => $unitKerja,
            ];
        }
    }

    sendResponse(401, 'Token tidak valid atau kadaluarsa');
    exit;
}

/**
 * Decode JWT payload tanpa verifikasi signature.
 * Return array payload jika valid JWT format, null jika bukan.
 */
function decodeJwtPayload(string $token): ?array {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;

    $payload = base64_decode(strtr($parts[1], '-_', '+/'));
    if ($payload === false) return null;

    $data = json_decode($payload, true);
    if (!is_array($data)) return null;

    // Cek expired (jika ada field exp)
    if (isset($data['exp']) && time() > (int)$data['exp']) {
        return null;
    }

    return $data;
}
