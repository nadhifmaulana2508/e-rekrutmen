<?php

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/JWT.php';

class AuthController {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * POST /api/auth/login
     * Coba SSO dulu, fallback ke DB admin lokal
     */
    public function login(array $data): void {
        // Support both id_peg (SSO) and username (local fallback)
        $id_peg   = trim($data['id_peg'] ?? $data['username'] ?? '');
        $password = (string)($data['password'] ?? '');

        if ($id_peg === '' || $password === '') {
            sendResponse(400, 'ID Pegawai dan password wajib diisi');
        }

        // === ATTEMPT 1: SSO API ===
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $is_localhost = in_array($host, ['localhost', '127.0.0.1'], true)
                    || str_starts_with($host, 'localhost:')
                    || str_starts_with($host, '127.0.0.1:');

        $ssoUrl = $is_localhost
            ? 'http://localhost/rest_api_sso/api/auth/login'
            : 'https://apisso.bkkjateng.co.id/api/auth/login';

        $postData = json_encode([
            'id_peg'   => $id_peg,
            'password' => $password,
            'app'      => 'sipatuh',
        ]);

        $ch = curl_init($ssoUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Accept: application/json'],
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Jika SSO berhasil, forward responsenya
        if ($response !== false && $httpCode === 200) {
            $result = json_decode($response, true);
            if (!empty($result['data']['token']) || (!empty($result['status']) && $result['status'] === 200)) {
                header('Content-Type: application/json');
                echo $response;
                exit;
            }
        }

        // === ATTEMPT 2: Fallback ke DB lokal (tabel admin) ===
        $stmt = $this->pdo->prepare('SELECT * FROM admin WHERE username = :u LIMIT 1');
        $stmt->execute([':u' => $id_peg]);
        $user = $stmt->fetch();

        $ok = false;
        if ($user) {
            $hash = (string)$user['password'];
            if (preg_match('/^\$2[aby]\$/', $hash) || str_starts_with($hash, '$argon')) {
                $ok = password_verify($password, $hash);
            } else {
                // fallback plaintext (dev only)
                $ok = hash_equals($hash, $password);
            }
        }

        if ($user && $ok) {
            $payload = [
                'id'       => (int)$user['id'],
                'username' => $user['username'],
                'nama'     => $user['nama'],
                'role'     => $user['role'] ?? 'admin',
                'iat'      => time(),
                'exp'      => time() + 60 * 60 * 8,
            ];
            $token = generateJWT($payload);
            sendResponse(200, 'Login berhasil', [
                'token' => $token,
                'user'  => [
                    'id'       => (int)$user['id'],
                    'username' => $user['username'],
                    'nama'     => $user['nama'],
                    'email'    => $user['email'] ?? '',
                    'role'     => $user['role'] ?? 'admin',
                ],
            ]);
        }

        // Kedua gagal
        if ($response !== false) {
            $result = json_decode($response, true);
            $msg = $result['message'] ?? $result['msg'] ?? null;
            sendResponse(401, $msg ?: 'ID Pegawai atau password salah');
        }
        sendResponse(401, 'ID Pegawai atau password salah');
    }

    /**
     * GET /api/auth/whoami
     * Verifikasi token SSO - proxy ke SSO whoami
     */
    public function whoami(string $token): void {
        $token = str_starts_with($token, 'Bearer ') ? substr($token, 7) : $token;

        if (!$token) sendResponse(401, 'Token tidak ditemukan');

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

        if ($response === false) {
            sendResponse(503, 'Tidak dapat terhubung ke server SSO');
        }

        // Forward response SSO
        http_response_code($httpCode);
        header('Content-Type: application/json');
        echo $response;
        exit;
    }
}
