<?php
// Deteksi environment
$is_localhost = in_array(($_SERVER['HTTP_HOST'] ?? ''), ['localhost', '127.0.0.1'], true);

if (!defined('BASE_URL')) {
    if ($is_localhost) {
        // Sesuaikan folder lokal jika berbeda
        define('BASE_URL', 'http://localhost/rekrutmen');
    } else {
        // Ganti dengan domain production
        define('BASE_URL', 'https://rekrutmen.example.com');
    }
}

if (!defined('API_URL')) {
    define('API_URL', BASE_URL . '/api');
}
