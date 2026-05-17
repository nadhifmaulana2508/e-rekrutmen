<?php

require_once __DIR__ . '/../helpers/response.php';

class DashboardController {
    private PDO $pdo;

    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    /** GET /api/dashboard/stats (admin) */
    public function stats(): void {
        try {
            // Filter parameters
            $tahun      = isset($_GET['tahun']) && $_GET['tahun'] !== '' ? (int)$_GET['tahun'] : null;
            $lowonganId = isset($_GET['lowongan_id']) && $_GET['lowongan_id'] !== '' ? (int)$_GET['lowongan_id'] : null;

            // Build lowongan filter conditions
            $lowonganWhere  = [];
            $lowonganParams = [];
            if ($tahun) {
                $lowonganWhere[] = 'YEAR(l.created_at) = :tahun';
                $lowonganParams[':tahun'] = $tahun;
            }
            if ($lowonganId) {
                $lowonganWhere[] = 'l.id = :lowongan_id';
                $lowonganParams[':lowongan_id'] = $lowonganId;
            }

            $lowonganFilter = $lowonganWhere ? ' WHERE ' . implode(' AND ', $lowonganWhere) : '';

            // Build pelamar filter (join with lowongan for tahun filter)
            $pelamarWhere  = [];
            $pelamarParams = [];
            if ($tahun) {
                $pelamarWhere[] = 'YEAR(l.created_at) = :tahun';
                $pelamarParams[':tahun'] = $tahun;
            }
            if ($lowonganId) {
                $pelamarWhere[] = 'p.id_lowongan = :lowongan_id';
                $pelamarParams[':lowongan_id'] = $lowonganId;
            }
            $pelamarFilter = $pelamarWhere ? ' WHERE ' . implode(' AND ', $pelamarWhere) : '';

            // Total lowongan (filtered)
            $sqlTotalLow = "SELECT COUNT(*) FROM lowongan l" . $lowonganFilter;
            $stmtTL = $this->pdo->prepare($sqlTotalLow);
            $stmtTL->execute($lowonganParams);
            $totalLowongan = (int)$stmtTL->fetchColumn();

            // Lowongan aktif (filtered)
            $aktifWhere = $lowonganWhere;
            $aktifWhere[] = "l.status = 'aktif'";
            $sqlAktif = "SELECT COUNT(*) FROM lowongan l WHERE " . implode(' AND ', $aktifWhere);
            $stmtA = $this->pdo->prepare($sqlAktif);
            $stmtA->execute($lowonganParams);
            $lowonganAktif = (int)$stmtA->fetchColumn();

            // Total pelamar (filtered)
            $sqlTP = "SELECT COUNT(*) FROM pelamar p LEFT JOIN lowongan l ON l.id = p.id_lowongan" . $pelamarFilter;
            $stmtTP = $this->pdo->prepare($sqlTP);
            $stmtTP->execute($pelamarParams);
            $totalPelamar = (int)$stmtTP->fetchColumn();

            // Pipeline per status (filtered)
            $sqlPipe = "SELECT p.status_lamaran AS status, COUNT(*) AS total
                        FROM pelamar p LEFT JOIN lowongan l ON l.id = p.id_lowongan"
                       . $pelamarFilter . " GROUP BY p.status_lamaran";
            $stmtPipe = $this->pdo->prepare($sqlPipe);
            $stmtPipe->execute($pelamarParams);
            $pipeline = $stmtPipe->fetchAll();

            // Top posisi (filtered)
            $sqlTop = "SELECT p.posisi_dilamar AS posisi, COUNT(*) AS total
                       FROM pelamar p LEFT JOIN lowongan l ON l.id = p.id_lowongan"
                      . $pelamarFilter . " GROUP BY p.posisi_dilamar ORDER BY total DESC LIMIT 10";
            $stmtTop = $this->pdo->prepare($sqlTop);
            $stmtTop->execute($pelamarParams);
            $topPosisi = $stmtTop->fetchAll();

            // Recent applications (filtered)
            $sqlRecent = "SELECT p.id, p.nama_lengkap, p.email, p.posisi_dilamar, p.penempatan,
                                 p.status_lamaran, p.created_at, l.judul AS judul_lowongan
                          FROM pelamar p
                          LEFT JOIN lowongan l ON l.id = p.id_lowongan"
                         . $pelamarFilter . " ORDER BY p.created_at DESC LIMIT 10";
            $stmtR = $this->pdo->prepare($sqlRecent);
            $stmtR->execute($pelamarParams);
            $recent = $stmtR->fetchAll();

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

    /** GET /api/dashboard/lowongan-list (admin) - for filter dropdown */
    public function lowonganList(): void {
        try {
            $tahun = isset($_GET['tahun']) && $_GET['tahun'] !== '' ? (int)$_GET['tahun'] : null;

            $sql = "SELECT id, judul, YEAR(created_at) AS tahun FROM lowongan";
            $params = [];
            if ($tahun) {
                $sql .= " WHERE YEAR(created_at) = :tahun";
                $params[':tahun'] = $tahun;
            }
            $sql .= " ORDER BY created_at DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll();

            sendResponse(200, 'Daftar lowongan untuk filter', $rows);
        } catch (Throwable $e) {
            sendResponse(500, 'Error: ' . $e->getMessage());
        }
    }

    /** GET /api/dashboard/tahun-list (admin) - distinct years */
    public function tahunList(): void {
        try {
            $rows = $this->pdo->query(
                "SELECT DISTINCT YEAR(created_at) AS tahun FROM lowongan ORDER BY tahun DESC"
            )->fetchAll();

            sendResponse(200, 'Daftar tahun', $rows);
        } catch (Throwable $e) {
            sendResponse(500, 'Error: ' . $e->getMessage());
        }
    }
}
