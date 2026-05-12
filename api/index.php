<?php

require_once __DIR__ . '/helpers/response.php';

$request  = $_GET['request'] ?? '';
$segments = explode('/', trim($request, '/'));
$endpoint = $segments[0] ?? '';

// simpan segments untuk dipakai di route
$_GET['segments'] = $segments;

switch ($endpoint) {
    case '':
        sendResponse(200, 'E-Form Rekrutmen API is running', [
            'version' => '1.0.0',
            'docs'    => [
                'POST   /api/auth/login',
                'GET    /api/auth/whoami',
                'GET    /api/lowongan           (public)',
                'GET    /api/lowongan/{id}      (public)',
                'POST   /api/lowongan           (admin)',
                'PUT    /api/lowongan/{id}      (admin)',
                'DELETE /api/lowongan/{id}      (admin)',
                'POST   /api/pelamar            (public multipart)',
                'GET    /api/pelamar            (admin)',
                'GET    /api/pelamar/{id}       (admin)',
                'PUT    /api/pelamar/{id}/status (admin)',
                'GET    /api/pelamar/track/{kode} (public)',
                'GET    /api/dashboard/stats    (admin)',
            ],
        ]);
        break;

    case 'auth':
        require __DIR__ . '/routes/auth.php';
        break;

    case 'lowongan':
        require __DIR__ . '/routes/lowongan.php';
        break;

    case 'pelamar':
        require __DIR__ . '/routes/pelamar.php';
        break;

    case 'dashboard':
        require __DIR__ . '/routes/dashboard.php';
        break;

    default:
        sendResponse(404, 'Endpoint tidak ditemukan');
}
