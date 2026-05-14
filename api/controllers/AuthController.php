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
     * Proxy ke SSO API (untuk backward compat jika FE panggil API lokal)
     */
    public function login(array $data): void {
        $id_peg   = trim($data['id_peg'] ?? '');
        $password = (string)($data['password'] ?? '');

        if ($id_peg === '' || $password === '') {
            sendResponse(400, 'ID Pegawai dan password wajib diisi');
        }

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
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false || $curlError) {
            sendResponse(503, 'Tidak dapat terhubung ke server SSO');
        }

        // Forward SSO response langsung
        $result = json_decode($response, true);
        http_response_code($httpCode);
        header('Content-Type: application/json');
        echo $response;
        exit;
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
