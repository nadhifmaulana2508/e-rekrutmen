<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/AuthController.php';

$ctrl     = new AuthController($pdo);
$method   = $_SERVER['REQUEST_METHOD'];
$segments = $_GET['segments'] ?? [];
$action   = $segments[1] ?? '';

switch ($action) {
    case 'login':
        if ($method !== 'POST') sendResponse(405, 'Metode tidak diizinkan');
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $ctrl->login($data);
        break;

    case 'whoami':
        if ($method !== 'GET') sendResponse(405, 'Metode tidak diizinkan');
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $tok = $headers['Authorization'] ?? $headers['authorization'] ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
        $ctrl->whoami($tok);
        break;

    default:
        sendResponse(404, 'Auth endpoint tidak ditemukan');
}
