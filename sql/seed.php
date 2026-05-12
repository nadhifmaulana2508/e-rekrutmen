<?php
/**
 * Seed script: Set password default admin -> "admin123" menggunakan password_hash() PHP
 * Cara pakai: jalankan via browser -> http://localhost/rekrutmen/sql/seed.php
 * atau CLI -> php sql/seed.php
 *
 * JANGAN taruh file ini di production!
 */
require_once __DIR__ . '/../api/config/database.php';

$plain = 'admin123';
$hash  = password_hash($plain, PASSWORD_BCRYPT);

$stmt = $pdo->prepare("UPDATE admin SET password = :p WHERE username = 'admin'");
$stmt->execute([':p' => $hash]);

echo "Password admin 'admin' berhasil di-set ke '{$plain}'\n";
echo "Hash: {$hash}\n";
