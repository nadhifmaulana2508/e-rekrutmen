<?php

/**
 * Kirim response JSON dengan format konsisten.
 * Struktur: { status, message, data }
 * NOTE: Gunakan key "status" (bukan "code") di seluruh client,
 * sesuai konvensi yang sudah diadopsi di project lelang PR #17.
 */
function sendResponse($status, $message, $data = null) {
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        http_response_code((int)$status);
    }

    echo json_encode([
        'status'  => (int)$status,
        'message' => $message,
        'data'    => $data,
    ]);
    exit;
}

// Tangani preflight OPTIONS global
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    http_response_code(204);
    exit;
}
