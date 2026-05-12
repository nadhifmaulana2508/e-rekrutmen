<?php

require_once __DIR__ . '/../helpers/response.php';

class DashboardController {
    private PDO $pdo;

    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    /** GET /api/dashboard/stats (admin) */
    public function stats(): void {
        $totalLowongan = (int)$this->pdo->query('SELECT COUNT(*) FROM lowongan')->fetchColumn();
        $lowonganAktif = (int)$this->pdo->query("SELECT COUNT(*) FROM lowongan WHERE status='aktif'")->fetchColumn();
        $totalPelamar  = (int)$this->pdo->query('SELECT COUNT(*) FROM pelamar')->fetchColumn();

        // Pipeline per status
        $pipeline = $this->pdo->query(
            "SELECT status_lamaran AS status, COUNT(*) AS total
             FROM pelamar GROUP BY status_lamaran"
        )->fetchAll();

        // Pelamar per lowongan (top 5)
        $topLowongan = $this->pdo->query(
            "SELECT l.id, l.judul, l.divisi, COUNT(p.id) AS total_pelamar
             FROM lowongan l
             LEFT JOIN pelamar p ON p.id_lowongan = l.id
             GROUP BY l.id
             ORDER BY total_pelamar DESC
             LIMIT 5"
        )->fetchAll();

        // Pelamar baru 7 hari terakhir
        $trend = $this->pdo->query(
            "SELECT DATE(created_at) AS tanggal, COUNT(*) AS total
             FROM pelamar
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
             GROUP BY DATE(created_at)
             ORDER BY tanggal ASC"
        )->fetchAll();

        // Recent applications
        $recent = $this->pdo->query(
            "SELECT p.id, p.nama_lengkap, p.email, p.status_lamaran, p.created_at,
                    l.judul AS judul_lowongan
             FROM pelamar p
             LEFT JOIN lowongan l ON l.id=p.id_lowongan
             ORDER BY p.created_at DESC LIMIT 8"
        )->fetchAll();

        sendResponse(200, 'Dashboard stats', [
            'total_lowongan'  => $totalLowongan,
            'lowongan_aktif'  => $lowonganAktif,
            'total_pelamar'   => $totalPelamar,
            'pipeline'        => $pipeline,
            'top_lowongan'    => $topLowongan,
            'trend_7_hari'    => $trend,
            'recent'          => $recent,
        ]);
    }
}
