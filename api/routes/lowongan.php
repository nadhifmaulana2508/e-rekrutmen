<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/LowonganController.php';
require_once __DIR__ . '/../middlewares/authMiddleware.php';

$ctrl     = new LowonganController($pdo);
$method   = $_SERVER['REQUEST_METHOD'];
$segments = $_GET['segments'] ?? [];
$id       = isset($segments[1]) && ctype_digit((string)$segments[1]) ? (int)$segments[1] : null;

switch ($method) {
    case 'GET':
        if ($id) $ctrl->show($id);
        else     $ctrl->index($_GET);
        break;

    case 'POST':
        $user = requireAuth();
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $ctrl->store($data, $user);
        break;

    case 'PUT':
    case 'PATCH':
        if (!$id) sendResponse(400, 'ID lowongan wajib');
        requireAuth();
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $ctrl->update($id, $data);
        break;

    case 'DELETE':
        if (!$id) sendResponse(400, 'ID lowongan wajib');
        requireAuth();
        $ctrl->destroy($id);
        break;

    default:
        sendResponse(405, 'Metode tidak diizinkan');
}
