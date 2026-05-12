<?php

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/upload.php';

class PelamarController {
    private PDO $pdo;
    private string $uploadRoot;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->uploadRoot = realpath(__DIR__ . '/../../uploads') ?: (__DIR__ . '/../../uploads');
    }

    /** POST /api/pelamar  (public, multipart/form-data) */
    public function store(array $data, array $files): void {
        $required = ['id_lowongan', 'nama_lengkap', 'email', 'telepon'];
        foreach ($required as $k) {
            if (empty($data[$k])) sendResponse(400, "Field {$k} wajib diisi");
        }

        $id_lowongan = (int)$data['id_lowongan'];

        // Pastikan lowongan ada & masih aktif
        $q = $this->pdo->prepare("SELECT id, status, deadline FROM lowongan WHERE id=:id");
        $q->execute([':id' => $id_lowongan]);
        $low = $q->fetch();
        if (!$low)                   sendResponse(404, 'Lowongan tidak ditemukan');
        if ($low['status'] !== 'aktif') sendResponse(400, 'Lowongan tidak lagi menerima pelamar');
        if (!empty($low['deadline']) && strtotime($low['deadline']) < strtotime(date('Y-m-d'))) {
            sendResponse(400, 'Deadline pelamaran sudah lewat');
        }

        // Validasi email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            sendResponse(400, 'Format email tidak valid');
        }

        // Cegah duplicate (email + lowongan)
        $dup = $this->pdo->prepare(
            'SELECT id FROM pelamar WHERE id_lowongan=:l AND email=:e'
        );
        $dup->execute([':l' => $id_lowongan, ':e' => $data['email']]);
        if ($dup->fetch()) {
            sendResponse(409, 'Anda sudah pernah melamar untuk lowongan ini');
        }

        // Upload foto & CV
        $fotoName = null;
        if (!empty($files['foto']['tmp_name'])) {
            $fotoName = uploadFoto($files['foto'], $this->uploadRoot . '/foto');
        }

        $cvName = null;
        if (!empty($files['cv']['tmp_name'])) {
            $cvName = uploadCv($files['cv'], $this->uploadRoot . '/cv');
        }
        if (!$cvName) sendResponse(400, 'CV (PDF) wajib diunggah');

        // Generate tracking code
        $kode = strtoupper('REK-' . date('Ymd') . '-' . bin2hex(random_bytes(3)));

        $stmt = $this->pdo->prepare(
            'INSERT INTO pelamar
             (id_lowongan, kode_tracking, nama_lengkap, email, telepon,
              tempat_lahir, tanggal_lahir, jenis_kelamin, alamat,
              pendidikan_terakhir, nama_institusi, jurusan, tahun_lulus, ipk,
              pengalaman_kerja, link_portfolio, foto, cv, status_lamaran)
             VALUES
             (:id_low, :kode, :nama, :email, :tel,
              :tl, :tgl, :jk, :alamat,
              :pend, :inst, :jur, :thn, :ipk,
              :peng, :link, :foto, :cv, "pending")'
        );

        $stmt->execute([
            ':id_low' => $id_lowongan,
            ':kode'   => $kode,
            ':nama'   => trim($data['nama_lengkap']),
            ':email'  => trim($data['email']),
            ':tel'    => trim($data['telepon']),
            ':tl'     => $data['tempat_lahir']         ?? null,
            ':tgl'    => $data['tanggal_lahir']        ?: null,
            ':jk'     => $data['jenis_kelamin']        ?: null,
            ':alamat' => $data['alamat']               ?? null,
            ':pend'   => $data['pendidikan_terakhir']  ?? null,
            ':inst'   => $data['nama_institusi']       ?? null,
            ':jur'    => $data['jurusan']              ?? null,
            ':thn'    => $data['tahun_lulus']          ?: null,
            ':ipk'    => $data['ipk']                  ?: null,
            ':peng'   => $data['pengalaman_kerja']     ?? null,
            ':link'   => $data['link_portfolio']       ?? null,
            ':foto'   => $fotoName,
            ':cv'     => $cvName,
        ]);

        sendResponse(201, 'Lamaran berhasil dikirim', [
            'id'            => (int)$this->pdo->lastInsertId(),
            'kode_tracking' => $kode,
        ]);
    }

    /** GET /api/pelamar (admin) */
    public function index(array $query): void {
        $where  = [];
        $params = [];

        if (!empty($query['status'])) {
            $where[] = 'p.status_lamaran = :st';
            $params[':st'] = $query['status'];
        }
        if (!empty($query['id_lowongan'])) {
            $where[] = 'p.id_lowongan = :l';
            $params[':l'] = (int)$query['id_lowongan'];
        }
        if (!empty($query['q'])) {
            $where[] = '(p.nama_lengkap LIKE :q OR p.email LIKE :q OR p.kode_tracking LIKE :q)';
            $params[':q'] = '%' . $query['q'] . '%';
        }

        $sql = 'SELECT p.*, l.judul AS judul_lowongan, l.divisi
                FROM pelamar p
                LEFT JOIN lowongan l ON l.id = p.id_lowongan';
        if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= ' ORDER BY p.created_at DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        sendResponse(200, 'Data pelamar', $stmt->fetchAll());
    }

    /** GET /api/pelamar/{id} (admin) */
    public function show(int $id): void {
        $stmt = $this->pdo->prepare(
            'SELECT p.*, l.judul AS judul_lowongan, l.divisi, l.lokasi, l.tipe_kerja
             FROM pelamar p LEFT JOIN lowongan l ON l.id=p.id_lowongan
             WHERE p.id=:id'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) sendResponse(404, 'Pelamar tidak ditemukan');
        sendResponse(200, 'Detail pelamar', $row);
    }

    /** PUT /api/pelamar/{id}/status (admin) */
    public function updateStatus(int $id, array $data): void {
        $allowed = ['pending','review','interview','diterima','ditolak'];
        $status  = $data['status_lamaran'] ?? '';
        if (!in_array($status, $allowed, true)) {
            sendResponse(400, 'Status tidak valid');
        }
        $stmt = $this->pdo->prepare(
            'UPDATE pelamar SET status_lamaran=:s, catatan_admin=:c WHERE id=:id'
        );
        $stmt->execute([
            ':s'  => $status,
            ':c'  => $data['catatan_admin'] ?? null,
            ':id' => $id,
        ]);
        if ($stmt->rowCount() === 0) sendResponse(404, 'Pelamar tidak ditemukan');
        sendResponse(200, 'Status pelamar diperbarui');
    }

    /** DELETE /api/pelamar/{id} (admin) */
    public function destroy(int $id): void {
        // Ambil nama file untuk cleanup
        $q = $this->pdo->prepare('SELECT foto, cv FROM pelamar WHERE id=:id');
        $q->execute([':id' => $id]);
        $row = $q->fetch();
        if (!$row) sendResponse(404, 'Pelamar tidak ditemukan');

        if (!empty($row['foto'])) safeDeleteFile($this->uploadRoot . '/foto/' . $row['foto']);
        if (!empty($row['cv']))   safeDeleteFile($this->uploadRoot . '/cv/'   . $row['cv']);

        $this->pdo->prepare('DELETE FROM pelamar WHERE id=:id')->execute([':id' => $id]);
        sendResponse(200, 'Pelamar berhasil dihapus');
    }

    /** GET /api/pelamar/track/{kode} (public) */
    public function track(string $kode): void {
        $stmt = $this->pdo->prepare(
            'SELECT p.kode_tracking, p.nama_lengkap, p.email, p.status_lamaran, p.catatan_admin,
                    p.created_at, p.updated_at,
                    l.judul AS judul_lowongan, l.divisi, l.lokasi
             FROM pelamar p LEFT JOIN lowongan l ON l.id = p.id_lowongan
             WHERE p.kode_tracking = :k'
        );
        $stmt->execute([':k' => strtoupper($kode)]);
        $row = $stmt->fetch();
        if (!$row) sendResponse(404, 'Kode tracking tidak ditemukan');
        sendResponse(200, 'Status lamaran', $row);
    }
}
