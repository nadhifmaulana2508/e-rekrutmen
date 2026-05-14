<?php
/**
 * Auth logic admin panel menggunakan SSO API BKK Jateng.
 * 
 * SSO API Endpoint:
 * - Production: https://apisso.bkkjateng.co.id/api/auth/login
 * - Lokal:      http://localhost/rest_api_sso/api/auth/login
 * 
 * Role superadmin jika unit_kerja (lowercase):
 * - "divisi operasional"
 * - "divisi sdm dan umum"
 */

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

// Tentukan SSO API URL berdasarkan environment
function getSsoApiUrl(): string {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $is_localhost = in_array($host, ['localhost', '127.0.0.1'], true)
                || str_starts_with($host, 'localhost:')
                || str_starts_with($host, '127.0.0.1:');

    if ($is_localhost) {
        return 'http://localhost/rest_api_sso/api/auth/login';
    }
    return 'https://apisso.bkkjateng.co.id/api/auth/login';
}

// Tentukan role berdasarkan unit_kerja dari SSO
function determineRole(string $unitKerja): string {
    $unit = strtolower(trim($unitKerja));
    $superadminUnits = [
        'divisi operasional',
        'divisi sdm dan umum',
    ];
    return in_array($unit, $superadminUnits, true) ? 'superadmin' : 'admin';
}

// Handle login POST
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['id_peg'], $_POST['password'])) {
    $id_peg  = trim($_POST['id_peg']);
    $password = (string)$_POST['password'];

    if ($id_peg === '' || $password === '') {
        $error_login = 'ID Pegawai dan password wajib diisi';
    } else {
        try {
            $ssoUrl = getSsoApiUrl();

            // Kirim request ke SSO API
            $postData = json_encode([
                'id_peg'   => $id_peg,
                'password' => $password,
                'app'      => 'rekrutmen',
            ]);

            $ch = curl_init($ssoUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $postData,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                ],
                CURLOPT_TIMEOUT        => 15,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => false, // produksi bisa di-enable
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($response === false || $curlError) {
                $error_login = 'Tidak dapat terhubung ke server SSO: ' . ($curlError ?: 'Unknown error');
            } else {
                $result = json_decode($response, true);

                // Cek response SSO berhasil
                // Support berbagai format response SSO:
                // Format 1: {"status": 200, "data": {...}}
                // Format 2: {"success": true, "data": {...}}
                // Format 3: {"token": "...", "user": {...}}
                $ssoSuccess = false;
                $ssoUser = null;

                if ($httpCode === 200) {
                    if (isset($result['data']) && is_array($result['data'])) {
                        $ssoSuccess = true;
                        $ssoUser = $result['data'];
                    } elseif (isset($result['user']) && is_array($result['user'])) {
                        $ssoSuccess = true;
                        $ssoUser = $result['user'];
                    } elseif (isset($result['status']) && $result['status'] === 200 && isset($result['data'])) {
                        $ssoSuccess = true;
                        $ssoUser = $result['data'];
                    } elseif (!empty($result['token'])) {
                        // Token-only response, gunakan username
                        $ssoSuccess = true;
                        $ssoUser = $result;
                    }
                }

                if ($ssoSuccess && $ssoUser) {
                    // Extract info dari SSO response
                    $nama     = $ssoUser['nama'] ?? $ssoUser['name'] ?? $ssoUser['nama_lengkap'] ?? $id_peg;
                    $email    = $ssoUser['email'] ?? ($id_peg . '@bkkjateng.co.id');
                    $unitKerja = $ssoUser['unit_kerja'] ?? $ssoUser['divisi'] ?? '';
                    $ssoToken = $result['token'] ?? $ssoUser['token'] ?? '';
                    $role     = determineRole($unitKerja);

                    // Generate JWT lokal untuk session admin panel
                    require_once __DIR__ . '/../../api/helpers/JWT.php';

                    $payload = [
                        'id'         => (int)($ssoUser['id'] ?? $ssoUser['id_peg'] ?? 0),
                        'id_peg'     => $id_peg,
                        'username'   => $ssoUser['username'] ?? $id_peg,
                        'nama'       => $nama,
                        'role'       => $role,
                        'unit_kerja' => $unitKerja,
                        'iat'        => time(),
                        'exp'        => time() + 60 * 60 * 8, // 8 jam
                    ];

                    $_SESSION['token'] = generateJWT($payload);
                    $_SESSION['user']  = [
                        'id'         => (int)($ssoUser['id'] ?? $ssoUser['id_peg'] ?? 0),
                        'id_peg'     => $id_peg,
                        'username'   => $ssoUser['username'] ?? $id_peg,
                        'nama'       => $nama,
                        'email'      => $email,
                        'role'       => $role,
                        'unit_kerja' => $unitKerja,
                    ];
                    $_SESSION['sso_token'] = $ssoToken;

                    header('Location: ' . BASE_URL . '/client/dashboard');
                    exit;
                } else {
                    // Login gagal
                    $msg = $result['message'] ?? $result['msg'] ?? $result['error'] ?? null;
                    $error_login = $msg ?: 'Username atau password salah';
                }
            }
        } catch (Throwable $e) {
            $error_login = 'Server error: ' . $e->getMessage();
        }
    }
}

// Check session
if (!empty($_SESSION['token']) && !empty($_SESSION['user'])) {
    $is_logged_in = true;
    $admin_user   = $_SESSION['user'];
}
