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
    // Pola dari PR #8 project lelang: hindari loopback HTTP, pakai PDO langsung.
    // NOTE: Jangan panggil AuthController->login() di sini, karena sendResponse()
    // pakai exit — response JSON-nya akan langsung dilempar ke browser.
    // Kita verifikasi password + generate JWT langsung di sini.
    require_once __DIR__ . '/../../api/config/database.php';
    require_once __DIR__ . '/../../api/helpers/JWT.php';

    $username = trim($_POST['username']);
    $password = (string)$_POST['password'];

    try {
        $stmt = $pdo->prepare('SELECT * FROM admin WHERE username = :u LIMIT 1');
        $stmt->execute([':u' => $username]);
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
                'role'     => $user['role'],
                'iat'      => time(),
                'exp'      => time() + 60 * 60 * 8,
            ];
            $_SESSION['token'] = generateJWT($payload);
            $_SESSION['user']  = [
                'id'       => (int)$user['id'],
                'username' => $user['username'],
                'nama'     => $user['nama'],
                'email'    => $user['email'],
                'role'     => $user['role'],
            ];
            header('Location: ' . BASE_URL . '/client/dashboard');
            exit;
        } else {
            $error_login = 'Username atau password salah';
        }
    } catch (Throwable $e) {
        $error_login = 'Server error: ' . $e->getMessage();
    }
}

// Check session
if (!empty($_SESSION['token']) && !empty($_SESSION['user'])) {
    $is_logged_in = true;
    $admin_user   = $_SESSION['user'];
}
