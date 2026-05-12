<?php

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/JWT.php';

class AuthController {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function login(array $data): void {
        $username = trim($data['username'] ?? '');
        $password = (string)($data['password'] ?? '');

        if ($username === '' || $password === '') {
            sendResponse(400, 'Username dan password wajib diisi');
        }

        $stmt = $this->pdo->prepare('SELECT * FROM admin WHERE username = :u LIMIT 1');
        $stmt->execute([':u' => $username]);
        $user = $stmt->fetch();

        // Dukung dua mode: bcrypt ATAU plain (untuk seed default dev).
        $ok = false;
        if ($user) {
            $hash = (string)$user['password'];
            if (str_starts_with($hash, '$2y$') || str_starts_with($hash, '$2b$') || str_starts_with($hash, '$argon')) {
                $ok = password_verify($password, $hash);
            } else {
                // fallback legacy/dev: plaintext compare
                $ok = hash_equals($hash, $password);
            }
        }

        if (!$user || !$ok) {
            sendResponse(401, 'Username atau password salah');
        }

        $payload = [
            'id'       => (int)$user['id'],
            'username' => $user['username'],
            'nama'     => $user['nama'],
            'role'     => $user['role'],
            'iat'      => time(),
            'exp'      => time() + 60 * 60 * 8, // 8 jam
        ];

        $token = generateJWT($payload);
        sendResponse(200, 'Login berhasil', [
            'token' => $token,
            'user'  => [
                'id'       => (int)$user['id'],
                'username' => $user['username'],
                'nama'     => $user['nama'],
                'email'    => $user['email'],
                'role'     => $user['role'],
            ],
        ]);
    }

    public function whoami(string $token): void {
        $token = str_starts_with($token, 'Bearer ') ? substr($token, 7) : $token;
        $decoded = verifyJWT($token);
        if (!$decoded) sendResponse(401, 'Token tidak valid atau kadaluarsa');

        $stmt = $this->pdo->prepare('SELECT id, username, nama, email, role FROM admin WHERE id = :id');
        $stmt->execute([':id' => $decoded['id'] ?? 0]);
        $user = $stmt->fetch();

        if (!$user) sendResponse(404, 'User tidak ditemukan');
        sendResponse(200, 'Data user', $user);
    }
}
