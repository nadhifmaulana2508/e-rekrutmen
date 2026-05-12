<?php

require_once __DIR__ . '/../helpers/response.php';

class LowonganController {
    private PDO $pdo;

    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    /** GET /api/lowongan (public) - filter: status, divisi, tipe_kerja, q */
    public function index(array $query): void {
        $where  = [];
        $params = [];

        // Public default: hanya yang aktif. Admin bisa kirim ?all=1 ?status=
        if (!empty($query['all'])) {
            if (!empty($query['status'])) {
                $where[] = 'status = :status';
                $params[':status'] = $query['status'];
            }
        } else {
            $where[] = "status = 'aktif'";
        }

        if (!empty($query['divisi'])) {
            $where[] = 'divisi = :divisi';
            $params[':divisi'] = $query['divisi'];
        }
        if (!empty($query['tipe_kerja'])) {
            $where[] = 'tipe_kerja = :tipe';
            $params[':tipe'] = $query['tipe_kerja'];
        }
        if (!empty($query['q'])) {
            $where[] = '(judul LIKE :q OR deskripsi LIKE :q OR lokasi LIKE :q)';
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

        sendResponse(200, 'Data lowongan', $rows);
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
        sendResponse(200, 'Detail lowongan', $row);
    }

    /** POST /api/lowongan (admin) */
    public function store(array $data, array $user): void {
        $required = ['judul', 'divisi', 'lokasi', 'deskripsi', 'requirements'];
        foreach ($required as $k) {
            if (empty($data[$k])) sendResponse(400, "Field {$k} wajib diisi");
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO lowongan
             (judul, divisi, lokasi, tipe_kerja, level, deskripsi, requirements, benefits,
              gaji_min, gaji_max, deadline, status, dibuat_oleh)
             VALUES
             (:judul, :divisi, :lokasi, :tipe, :level, :desk, :req, :ben,
              :gmin, :gmax, :deadline, :status, :author)'
        );
        $stmt->execute([
            ':judul'    => $data['judul'],
            ':divisi'   => $data['divisi'],
            ':lokasi'   => $data['lokasi'],
            ':tipe'     => $data['tipe_kerja']   ?? 'full_time',
            ':level'    => $data['level']        ?? 'junior',
            ':desk'     => $data['deskripsi'],
            ':req'      => $data['requirements'],
            ':ben'      => $data['benefits']     ?? null,
            ':gmin'     => $data['gaji_min']     ?: null,
            ':gmax'     => $data['gaji_max']     ?: null,
            ':deadline' => $data['deadline']     ?: null,
            ':status'   => $data['status']       ?? 'aktif',
            ':author'   => $user['id']           ?? null,
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
        $allowed = ['judul','divisi','lokasi','tipe_kerja','level','deskripsi',
                    'requirements','benefits','gaji_min','gaji_max','deadline','status'];

        foreach ($allowed as $f) {
            if (array_key_exists($f, $data)) {
                $fields[] = "$f = :$f";
                $params[":$f"] = ($data[$f] === '' ? null : $data[$f]);
            }
        }
        if (!$fields) sendResponse(400, 'Tidak ada data yang diubah');

        $sql = 'UPDATE lowongan SET ' . implode(', ', $fields) . ' WHERE id=:id';
        $this->pdo->prepare($sql)->execute($params);
        sendResponse(200, 'Lowongan berhasil diperbarui');
    }

    /** DELETE /api/lowongan/{id} (admin) */
    public function destroy(int $id): void {
        // CASCADE di skema akan ikut hapus pelamar. Pastikan aman.
        $stmt = $this->pdo->prepare('DELETE FROM lowongan WHERE id=:id');
        $stmt->execute([':id' => $id]);
        if ($stmt->rowCount() === 0) sendResponse(404, 'Lowongan tidak ditemukan');
        sendResponse(200, 'Lowongan berhasil dihapus');
    }
}
