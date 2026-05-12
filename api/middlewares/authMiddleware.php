<?php

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/JWT.php';

/**
 * Pastikan request membawa Bearer token valid.
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
    } else {
        sendResponse(401, 'Token tidak ditemukan');
    }

    $user = verifyJWT($token);
    if (!$user) {
        sendResponse(401, 'Token tidak valid atau kadaluarsa');
    }
    return $user;
}
