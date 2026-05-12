<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/PelamarController.php';
require_once __DIR__ . '/../middlewares/authMiddleware.php';

$ctrl     = new PelamarController($pdo);
$method   = $_SERVER['REQUEST_METHOD'];
$segments = $_GET['segments'] ?? [];
$seg1     = $segments[1] ?? '';
$seg2     = $segments[2] ?? '';

// ===== PUBLIC ENDPOINTS =====

// GET /api/pelamar/track/{kode}  (public)
if ($seg1 === 'track' && $method === 'GET') {
    if ($seg2 === '') sendResponse(400, 'Kode tracking wajib');
    $ctrl->track($seg2);
}

// POST /api/pelamar  (public, multipart/form-data)
if ($seg1 === '' && $method === 'POST') {
    // Untuk multipart, data ada di $_POST
    $data = !empty($_POST) ? $_POST : (json_decode(file_get_contents('php://input'), true) ?? []);
    $ctrl->store($data, $_FILES);
}

// ===== ADMIN ENDPOINTS =====

$id = ctype_digit((string)$seg1) ? (int)$seg1 : null;

switch ($method) {
    case 'GET':
        requireAuth();
        if ($id) $ctrl->show($id);
        else     $ctrl->index($_GET);
        break;

    case 'PUT':
    case 'PATCH':
        requireAuth();
        if (!$id)                   sendResponse(400, 'ID pelamar wajib');
        if ($seg2 !== 'status')     sendResponse(404, 'Endpoint tidak ditemukan');
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $ctrl->updateStatus($id, $data);
        break;

    case 'DELETE':
        requireAuth();
        if (!$id) sendResponse(400, 'ID pelamar wajib');
        $ctrl->destroy($id);
        break;

    default:
        sendResponse(405, 'Metode tidak diizinkan');
}
