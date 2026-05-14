<?php

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/JWT.php';

class AuthController {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Tentukan SSO API URL berdasarkan environment
     */
    private function getSsoApiUrl(): string {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $is_localhost = in_array($host, ['localhost', '127.0.0.1'], true)
                    || str_starts_with($host, 'localhost:')
                    || str_starts_with($host, '127.0.0.1:');

        if ($is_localhost) {
            return 'http://localhost/rest_api_sso/api/auth/login';
        }
        return 'https://apisso.bkkjateng.co.id/api/auth/login';
    }

    /**
     * Tentukan role berdasarkan unit_kerja dari SSO
     */
    private function determineRole(string $unitKerja): string {
        $unit = strtolower(trim($unitKerja));
        $superadminUnits = [
            'divisi operasional',
            'divisi sdm dan umum',
        ];
        return in_array($unit, $superadminUnits, true) ? 'superadmin' : 'admin';
    }

    public function login(array $data): void {
        $username = trim($data['username'] ?? '');
        $password = (string)($data['password'] ?? '');

        if ($username === '' || $password === '') {
            sendResponse(400, 'Username dan password wajib diisi');
        }

        // === LOGIN VIA SSO API ===
        $ssoUrl = $this->getSsoApiUrl();
        $postData = json_encode([
            'username' => $username,
            'password' => $password,
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
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false || $curlError) {
            sendResponse(503, 'Tidak dapat terhubung ke server SSO');
        }

        $result = json_decode($response, true);

        // Cek response SSO
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
                $ssoSuccess = true;
                $ssoUser = $result;
            }
        }

        if (!$ssoSuccess || !$ssoUser) {
            $msg = $result['message'] ?? $result['msg'] ?? $result['error'] ?? 'Username atau password salah';
            sendResponse(401, $msg);
        }

        // Extract data dari SSO
        $nama      = $ssoUser['nama'] ?? $ssoUser['name'] ?? $ssoUser['nama_lengkap'] ?? $username;
        $email     = $ssoUser['email'] ?? ($username . '@bkkjateng.co.id');
        $unitKerja = $ssoUser['unit_kerja'] ?? $ssoUser['divisi'] ?? '';
        $ssoToken  = $result['token'] ?? $ssoUser['token'] ?? '';
        $role      = $this->determineRole($unitKerja);

        // Generate JWT lokal
        $payload = [
            'id'         => (int)($ssoUser['id'] ?? 0),
            'username'   => $username,
            'nama'       => $nama,
            'role'       => $role,
            'unit_kerja' => $unitKerja,
            'iat'        => time(),
            'exp'        => time() + 60 * 60 * 8, // 8 jam
        ];

        $token = generateJWT($payload);
        sendResponse(200, 'Login berhasil', [
            'token' => $token,
            'user'  => [
                'id'         => (int)($ssoUser['id'] ?? 0),
                'username'   => $username,
                'nama'       => $nama,
                'email'      => $email,
                'role'       => $role,
                'unit_kerja' => $unitKerja,
            ],
        ]);
    }

    public function whoami(string $token): void {
        $token = str_starts_with($token, 'Bearer ') ? substr($token, 7) : $token;
        $decoded = verifyJWT($token);
        if (!$decoded) sendResponse(401, 'Token tidak valid atau kadaluarsa');

        // Return data dari JWT (karena SSO, tidak perlu query DB lokal)
        sendResponse(200, 'Data user', [
            'id'         => $decoded['id'] ?? 0,
            'username'   => $decoded['username'] ?? '',
            'nama'       => $decoded['nama'] ?? '',
            'role'       => $decoded['role'] ?? 'admin',
            'unit_kerja' => $decoded['unit_kerja'] ?? '',
        ]);
    }
}
