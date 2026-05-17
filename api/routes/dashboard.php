<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/DashboardController.php';
require_once __DIR__ . '/../middlewares/authMiddleware.php';

$ctrl     = new DashboardController($pdo);
$method   = $_SERVER['REQUEST_METHOD'];
$segments = $_GET['segments'] ?? [];
$action   = $segments[1] ?? '';

if ($method !== 'GET') sendResponse(405, 'Metode tidak diizinkan');
requireAuth();

switch ($action) {
    case 'stats':
        $ctrl->stats();
        break;
    case 'lowongan-list':
        $ctrl->lowonganList();
        break;
    case 'tahun-list':
        $ctrl->tahunList();
        break;
    default:
        sendResponse(404, 'Endpoint dashboard tidak ditemukan');
}
