<?php

require_once __DIR__ . '/../helpers/response.php';

class DashboardController {
    private PDO $pdo;

    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    /** GET /api/dashboard/stats (admin) */
    public function stats(): void {
        try {
            $totalLowongan = (int)$this->pdo->query('SELECT COUNT(*) FROM lowongan')->fetchColumn();
            $lowonganAktif = (int)$this->pdo->query("SELECT COUNT(*) FROM lowongan WHERE status='aktif'")->fetchColumn();
            $totalPelamar  = (int)$this->pdo->query('SELECT COUNT(*) FROM pelamar')->fetchColumn();

            // Pipeline per status
            $pipeline = $this->pdo->query(
                "SELECT status_lamaran AS status, COUNT(*) AS total
                 FROM pelamar GROUP BY status_lamaran"
            )->fetchAll();

            // Pelamar per posisi (top 10)
            $topPosisi = $this->pdo->query(
                "SELECT posisi_dilamar AS posisi, COUNT(*) AS total
                 FROM pelamar GROUP BY posisi_dilamar ORDER BY total DESC LIMIT 10"
            )->fetchAll();

            // Recent applications
            $recent = $this->pdo->query(
                "SELECT p.id, p.nama_lengkap, p.email, p.posisi_dilamar, p.penempatan,
                        p.status_lamaran, p.created_at, l.judul AS judul_lowongan
                 FROM pelamar p
                 LEFT JOIN lowongan l ON l.id=p.id_lowongan
                 ORDER BY p.created_at DESC LIMIT 10"
            )->fetchAll();

            sendResponse(200, 'Dashboard stats', [
                'total_lowongan'  => $totalLowongan,
                'lowongan_aktif'  => $lowonganAktif,
                'total_pelamar'   => $totalPelamar,
                'pipeline'        => $pipeline,
                'top_posisi'      => $topPosisi,
                'recent'          => $recent,
            ]);
        } catch (Throwable $e) {
            sendResponse(500, 'Error dashboard: ' . $e->getMessage());
        }
    }
}
