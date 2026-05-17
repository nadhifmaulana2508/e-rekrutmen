<?php

require_once __DIR__ . '/../helpers/response.php';

class LowonganController {
    private PDO $pdo;

    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    /** GET /api/lowongan (public) */
    public function index(array $query): void {
        try {
            $where  = [];
            $params = [];

            // Hanya filter string params yang valid (exclude internal routing keys)
            $routingKeys = ['url', 'request', 'segments', 'page', 'id'];

            if (!empty($query['all'])) {
                if (!empty($query['status'])) {
                    $where[] = 'status = :status';
                    $params[':status'] = $query['status'];
                }
            } else {
                $where[] = "status = 'aktif'";
            }

            if (!empty($query['q']) && is_string($query['q'])) {
                $where[] = '(judul LIKE :q OR deskripsi LIKE :q)';
                $params[':q'] = '%' . $query['q'] . '%';
            }

            $sql = 'SELECT l.*, 
                      (SELECT COUNT(*) FROM pelamar p WHERE p.id_lowongan = l.id) AS jumlah_pelamar
                    FROM lowongan l';
            if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
            $sql .= ' ORDER BY l.created_at DESC';

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll();

            // Decode JSON fields
            foreach ($rows as &$r) {
                $r['posisi_tersedia']     = json_decode($r['posisi_tersedia'] ?? '[]', true) ?: [];
                $r['penempatan_tersedia'] = json_decode($r['penempatan_tersedia'] ?? '[]', true) ?: [];
            }

            sendResponse(200, 'Data lowongan', $rows);
        } catch (Throwable $e) {
            sendResponse(500, 'Error query lowongan: ' . $e->getMessage());
        }
    }

    /** GET /api/lowongan/{id} (public) */
    public function show(int $id): void {
        $stmt = $this->pdo->prepare(
            'SELECT l.*, 
                (SELECT COUNT(*) FROM pelamar p WHERE p.id_lowongan = l.id) AS jumlah_pelamar
             FROM lowongan l WHERE l.id = :id'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) sendResponse(404, 'Lowongan tidak ditemukan');

        $row['posisi_tersedia']     = json_decode($row['posisi_tersedia'] ?? '[]', true) ?: [];
        $row['penempatan_tersedia'] = json_decode($row['penempatan_tersedia'] ?? '[]', true) ?: [];

        sendResponse(200, 'Detail lowongan', $row);
    }

    /** POST /api/lowongan (admin) */
    public function store(array $data, array $user): void {
        $required = ['judul', 'posisi_tersedia', 'penempatan_tersedia'];
        foreach ($required as $k) {
            if (empty($data[$k])) sendResponse(400, "Field {$k} wajib diisi");
        }

        // Encode arrays to JSON if passed as array
        $posisi     = is_array($data['posisi_tersedia'])     ? json_encode($data['posisi_tersedia'])     : $data['posisi_tersedia'];
        $penempatan = is_array($data['penempatan_tersedia']) ? json_encode($data['penempatan_tersedia']) : $data['penempatan_tersedia'];

        // Determine dibuat_oleh: hanya set jika user adalah admin lokal (ada di tabel admin)
        // SSO user tidak punya ID di tabel admin → set NULL agar tidak melanggar FK constraint
        $authorId = null;
        if (!empty($user['id'])) {
            // Cek apakah ID ini ada di tabel admin (local admin)
            $checkAdmin = $this->pdo->prepare('SELECT id FROM admin WHERE id = :id LIMIT 1');
            $checkAdmin->execute([':id' => (int)$user['id']]);
            if ($checkAdmin->fetch()) {
                $authorId = (int)$user['id'];
            }
        }

        // Simpan nama pembuat (baik admin lokal maupun SSO user)
        $authorNama = $user['nama'] ?? $user['username'] ?? 'Unknown';

        $stmt = $this->pdo->prepare(
            'INSERT INTO lowongan
             (judul, deskripsi, persyaratan, posisi_tersedia, penempatan_tersedia, deadline, status, dibuat_oleh, dibuat_oleh_nama)
             VALUES
             (:judul, :desk, :syarat, :posisi, :penempatan, :deadline, :status, :author, :author_nama)'
        );
        $stmt->execute([
            ':judul'        => $data['judul'],
            ':desk'         => $data['deskripsi']       ?? null,
            ':syarat'       => $data['persyaratan']     ?? null,
            ':posisi'       => $posisi,
            ':penempatan'   => $penempatan,
            ':deadline'     => $data['deadline']        ?: null,
            ':status'       => $data['status']          ?? 'aktif',
            ':author'       => $authorId,
            ':author_nama'  => $authorNama,
        ]);
        $id = (int)$this->pdo->lastInsertId();
        sendResponse(201, 'Lowongan berhasil dibuat', ['id' => $id]);
    }

    /** PUT /api/lowongan/{id} (admin) */
    public function update(int $id, array $data): void {
        $check = $this->pdo->prepare('SELECT id FROM lowongan WHERE id=:id');
        $check->execute([':id' => $id]);
        if (!$check->fetch()) sendResponse(404, 'Lowongan tidak ditemukan');

        $fields = [];
        $params = [':id' => $id];
        $allowed = ['judul','deskripsi','persyaratan','posisi_tersedia','penempatan_tersedia','deadline','status'];

        foreach ($allowed as $f) {
            if (array_key_exists($f, $data)) {
                $val = $data[$f];
                // Encode arrays to JSON
                if (in_array($f, ['posisi_tersedia', 'penempatan_tersedia']) && is_array($val)) {
                    $val = json_encode($val);
                }
                $fields[] = "$f = :$f";
                $params[":$f"] = ($val === '' ? null : $val);
            }
        }
        if (!$fields) sendResponse(400, 'Tidak ada data yang diubah');

        $sql = 'UPDATE lowongan SET ' . implode(', ', $fields) . ' WHERE id=:id';
        $this->pdo->prepare($sql)->execute($params);
        sendResponse(200, 'Lowongan berhasil diperbarui');
    }

    /** DELETE /api/lowongan/{id} (admin) */
    public function destroy(int $id): void {
        $stmt = $this->pdo->prepare('DELETE FROM lowongan WHERE id=:id');
        $stmt->execute([':id' => $id]);
        if ($stmt->rowCount() === 0) sendResponse(404, 'Lowongan tidak ditemukan');
        sendResponse(200, 'Lowongan berhasil dihapus');
    }
}
