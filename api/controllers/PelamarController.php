<?php

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/upload.php';
require_once __DIR__ . '/../helpers/mailer.php';

class PelamarController {
    private PDO $pdo;
    private string $uploadRoot;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->uploadRoot = realpath(__DIR__ . '/../../uploads') ?: (__DIR__ . '/../../uploads');
    }

    /** POST /api/pelamar (public, multipart/form-data) */
    public function store(array $data, array $files): void {
        // Validasi minimal - hanya id_lowongan wajib
        if (empty($data['id_lowongan'])) sendResponse(400, "Field 'id_lowongan' wajib diisi");

        // Set default '-' untuk field NOT NULL yang mungkin kosong
        $defaultDash = ['posisi_dilamar','penempatan','tempat_lahir','alamat_ktp',
                        'alamat_domisili','pendidikan_terakhir','nama_institusi','jurusan',
                        'nama_lengkap','nomor_ktp','no_hp','email'];
        foreach ($defaultDash as $f) {
            if (empty($data[$f])) $data[$f] = '-';
        }
        // Default jenis_kelamin
        if (empty($data['jenis_kelamin'])) $data['jenis_kelamin'] = 'Laki-laki';
        // Default tanggal_lahir & tahun_lulus
        if (empty($data['tanggal_lahir'])) $data['tanggal_lahir'] = '2000-01-01';
        if (empty($data['tahun_lulus'])) $data['tahun_lulus'] = date('Y');

        $id_lowongan = (int)$data['id_lowongan'];

        // Cek lowongan aktif
        $q = $this->pdo->prepare("SELECT id, status, deadline FROM lowongan WHERE id=:id");
        $q->execute([':id' => $id_lowongan]);
        $low = $q->fetch();
        if (!$low) sendResponse(404, 'Lowongan tidak ditemukan');
        if ($low['status'] !== 'aktif') sendResponse(400, 'Lowongan sudah ditutup');
        if (!empty($low['deadline']) && strtotime($low['deadline']) < strtotime(date('Y-m-d'))) {
            sendResponse(400, 'Deadline pelamaran sudah lewat');
        }

        // Validasi email (skip jika default '-')
        if ($data['email'] !== '-' && !empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $data['email'] = '-';
        }

        // Skip validasi NIK dan HP untuk testing

        // Cegah duplicate (email + lowongan) - skip jika email = '-'
        if ($data['email'] !== '-') {
            $dup = $this->pdo->prepare('SELECT id FROM pelamar WHERE id_lowongan=:l AND email=:e');
            $dup->execute([':l' => $id_lowongan, ':e' => $data['email']]);
            if ($dup->fetch()) {
                sendResponse(409, 'Anda sudah pernah melamar untuk lowongan ini');
            }
        }

        // Upload foto 3x4
        $foto3x4 = null;
        if (!empty($files['foto_3x4']['tmp_name'])) {
            $foto3x4 = uploadFoto($files['foto_3x4'], $this->uploadRoot . '/foto', 'foto3x4');
        }

        // Upload foto full body
        $fotoFull = null;
        if (!empty($files['foto_full_body']['tmp_name'])) {
            $fotoFull = uploadFoto($files['foto_full_body'], $this->uploadRoot . '/foto', 'fotofull');
        }

        // Generate tracking code
        $kode = strtoupper('BPR-' . date('Ymd') . '-' . bin2hex(random_bytes(3)));

        // Encode JSON fields
        $kemampuanKomputer = $data['kemampuan_komputer'] ?? null;
        if (is_array($kemampuanKomputer)) $kemampuanKomputer = json_encode($kemampuanKomputer);

        $sim = $data['sim'] ?? null;
        if (is_array($sim)) $sim = json_encode($sim);

        $khususItBidang = $data['khusus_it_bidang'] ?? null;
        if (is_array($khususItBidang)) $khususItBidang = json_encode($khususItBidang);

        // Pernyataan check (skip untuk testing - terima apapun)

        $stmt = $this->pdo->prepare(
            'INSERT INTO pelamar (
                id_lowongan, kode_tracking,
                posisi_dilamar, penempatan, bersedia_seluruh_wilayah,
                sumber_informasi, sumber_informasi_lainnya, ekspektasi_gaji,
                ketersediaan_mulai, ketersediaan_mulai_lainnya,
                nama_lengkap, nama_panggilan, jenis_kelamin, tempat_lahir, tanggal_lahir,
                status_pernikahan, agama, kewarganegaraan, nomor_ktp, npwp,
                alamat_ktp, alamat_domisili, no_hp, email, akun_linkedin,
                foto_3x4, foto_full_body,
                pendidikan_terakhir, pendidikan_lainnya, nama_institusi, jurusan,
                tahun_masuk, tahun_lulus, ipk, prestasi,
                kemampuan_komputer, kemampuan_komputer_lainnya, bahasa_asing,
                sertifikasi, keahlian_khusus, sim,
                khusus_pengalaman_marketing, khusus_relasi_bisnis, khusus_bersedia_target,
                khusus_kendaraan_pribadi, khusus_it_bidang, khusus_it_bahasa_pemrograman,
                khusus_analis_lapkeu, khusus_analis_appraisal,
                pernah_kasus_hukum, hubungan_keluarga_pegawai, hubungan_keluarga_detail,
                status_slik, riwayat_penyakit, riwayat_penyakit_detail,
                pernyataan_data_benar, pernyataan_ikut_proses, pernyataan_setuju_data,
                tanggal_pengisian, tanda_tangan, status_lamaran
            ) VALUES (
                :id_lowongan, :kode,
                :posisi, :penempatan, :bersedia,
                :sumber, :sumber_lain, :gaji,
                :mulai, :mulai_lain,
                :nama, :panggilan, :jk, :tempat_lahir, :tgl_lahir,
                :nikah, :agama, :warga, :ktp, :npwp,
                :alamat_ktp, :alamat_dom, :hp, :email, :linkedin,
                :foto3x4, :fotofull,
                :pend, :pend_lain, :institusi, :jurusan,
                :thn_masuk, :thn_lulus, :ipk, :prestasi,
                :komputer, :komputer_lain, :bahasa,
                :sertif, :keahlian, :sim,
                :k_marketing, :k_relasi, :k_target,
                :k_kendaraan, :k_it_bidang, :k_it_lang,
                :k_lapkeu, :k_appraisal,
                :kasus, :keluarga, :keluarga_detail,
                :slik, :penyakit, :penyakit_detail,
                :p1, :p2, :p3,
                :tgl_isi, :ttd, "pending"
            )'
        );

        $stmt->execute([
            ':id_lowongan'    => $id_lowongan,
            ':kode'           => $kode,
            ':posisi'         => $data['posisi_dilamar'],
            ':penempatan'     => $data['penempatan'],
            ':bersedia'       => $data['bersedia_seluruh_wilayah'] ?? 'Ya',
            ':sumber'         => $data['sumber_informasi'] ?? null,
            ':sumber_lain'    => $data['sumber_informasi_lainnya'] ?? null,
            ':gaji'           => !empty($data['ekspektasi_gaji']) ? (int)$data['ekspektasi_gaji'] : null,
            ':mulai'          => $data['ketersediaan_mulai'] ?? null,
            ':mulai_lain'     => $data['ketersediaan_mulai_lainnya'] ?? null,
            ':nama'           => trim($data['nama_lengkap']),
            ':panggilan'      => $data['nama_panggilan'] ?? null,
            ':jk'             => $data['jenis_kelamin'],
            ':tempat_lahir'   => $data['tempat_lahir'],
            ':tgl_lahir'      => $data['tanggal_lahir'],
            ':nikah'          => self::validateEnum($data['status_pernikahan'] ?? '', ['Belum Menikah','Menikah','Cerai']),
            ':agama'          => $data['agama'] ?? null,
            ':warga'          => $data['kewarganegaraan'] ?? 'Indonesia',
            ':ktp'            => $data['nomor_ktp'],
            ':npwp'           => $data['npwp'] ?? null,
            ':alamat_ktp'     => $data['alamat_ktp'],
            ':alamat_dom'     => $data['alamat_domisili'],
            ':hp'             => $data['no_hp'],
            ':email'          => trim($data['email']),
            ':linkedin'       => $data['akun_linkedin'] ?? null,
            ':foto3x4'        => $foto3x4,
            ':fotofull'       => $fotoFull,
            ':pend'           => $data['pendidikan_terakhir'],
            ':pend_lain'      => $data['pendidikan_lainnya'] ?? null,
            ':institusi'      => $data['nama_institusi'],
            ':jurusan'        => $data['jurusan'],
            ':thn_masuk'      => !empty($data['tahun_masuk']) ? (int)$data['tahun_masuk'] : null,
            ':thn_lulus'      => (int)$data['tahun_lulus'],
            ':ipk'            => $data['ipk'] ?? null,
            ':prestasi'       => $data['prestasi'] ?? null,
            ':komputer'       => $kemampuanKomputer,
            ':komputer_lain'  => $data['kemampuan_komputer_lainnya'] ?? null,
            ':bahasa'         => $data['bahasa_asing'] ?? null,
            ':sertif'         => $data['sertifikasi'] ?? null,
            ':keahlian'       => $data['keahlian_khusus'] ?? null,
            ':sim'            => $sim,
            ':k_marketing'    => $data['khusus_pengalaman_marketing'] ?? null,
            ':k_relasi'       => $data['khusus_relasi_bisnis'] ?? null,
            ':k_target'       => $data['khusus_bersedia_target'] ?? null,
            ':k_kendaraan'    => $data['khusus_kendaraan_pribadi'] ?? null,
            ':k_it_bidang'    => $khususItBidang,
            ':k_it_lang'      => $data['khusus_it_bahasa_pemrograman'] ?? null,
            ':k_lapkeu'       => $data['khusus_analis_lapkeu'] ?? null,
            ':k_appraisal'    => $data['khusus_analis_appraisal'] ?? null,
            ':kasus'          => $data['pernah_kasus_hukum'] ?? 'Tidak',
            ':keluarga'       => $data['hubungan_keluarga_pegawai'] ?? 'Tidak',
            ':keluarga_detail'=> $data['hubungan_keluarga_detail'] ?? null,
            ':slik'           => $data['status_slik'] ?? null,
            ':penyakit'       => $data['riwayat_penyakit'] ?? null,
            ':penyakit_detail'=> $data['riwayat_penyakit_detail'] ?? null,
            ':p1'             => 1,
            ':p2'             => 1,
            ':p3'             => 1,
            ':tgl_isi'        => date('Y-m-d'),
            ':ttd'            => trim($data['nama_lengkap']),
        ]);

        $pelamarId = (int)$this->pdo->lastInsertId();

        // Simpan pengalaman kerja (multiple)
        if (!empty($data['pengalaman'])) {
            $pengalaman = is_string($data['pengalaman']) ? json_decode($data['pengalaman'], true) : $data['pengalaman'];
            if (is_array($pengalaman)) {
                $stmtP = $this->pdo->prepare(
                    'INSERT INTO pelamar_pengalaman (id_pelamar, nama_perusahaan, jabatan, periode_kerja, deskripsi_pekerjaan, gaji_terakhir, alasan_berhenti)
                     VALUES (:pid, :perusahaan, :jabatan, :periode, :desk, :gaji, :alasan)'
                );
                foreach ($pengalaman as $exp) {
                    if (empty($exp['nama_perusahaan'])) continue;
                    $stmtP->execute([
                        ':pid'        => $pelamarId,
                        ':perusahaan' => $exp['nama_perusahaan'],
                        ':jabatan'    => $exp['jabatan'] ?? null,
                        ':periode'    => $exp['periode_kerja'] ?? null,
                        ':desk'       => $exp['deskripsi_pekerjaan'] ?? null,
                        ':gaji'       => !empty($exp['gaji_terakhir']) ? (int)$exp['gaji_terakhir'] : null,
                        ':alasan'     => $exp['alasan_berhenti'] ?? null,
                    ]);
                }
            }
        }

        // Upload dokumen (semua opsional untuk testing)
        $dokumenTypes = [
            'surat_lamaran', 'cv', 'ktp', 'kk', 'ijazah', 'transkrip',
            'surat_sehat', 'sertifikat', 'surat_kerja', 'portfolio'
        ];
        $stmtD = $this->pdo->prepare(
            'INSERT INTO pelamar_dokumen (id_pelamar, jenis_dokumen, nama_file, nama_asli, ukuran)
             VALUES (:pid, :jenis, :nama, :asli, :ukuran)'
        );
        foreach ($dokumenTypes as $docType) {
            $key = 'dokumen_' . $docType;
            if (!empty($files[$key]['tmp_name']) && $files[$key]['error'] === UPLOAD_ERR_OK) {
                $uploaded = uploadDokumen($files[$key], $this->uploadRoot . '/cv', $docType);
                if ($uploaded) {
                    $stmtD->execute([
                        ':pid'    => $pelamarId,
                        ':jenis'  => $docType,
                        ':nama'   => $uploaded,
                        ':asli'   => $files[$key]['name'] ?? null,
                        ':ukuran' => $files[$key]['size'] ?? null,
                    ]);
                }
            }
        }

        // TODO: Kirim email kode tracking (uncomment setelah composer install + setup SMTP)
        // $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        // $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
        // $baseUrl  = $protocol . '://' . $host . dirname(dirname($_SERVER['SCRIPT_NAME']));
        // sendTrackingEmail(trim($data['email']), trim($data['nama_lengkap']), $kode, $data['posisi_dilamar'], rtrim($baseUrl, '/'));

        sendResponse(201, 'Lamaran berhasil dikirim! Simpan kode tracking Anda.', [
            'id'            => $pelamarId,
            'kode_tracking' => $kode,
            'email'         => trim($data['email']),
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
        if (!empty($query['posisi'])) {
            $where[] = 'p.posisi_dilamar = :pos';
            $params[':pos'] = $query['posisi'];
        }
        if (!empty($query['q'])) {
            $where[] = '(p.nama_lengkap LIKE :q OR p.email LIKE :q OR p.kode_tracking LIKE :q OR p.posisi_dilamar LIKE :q)';
            $params[':q'] = '%' . $query['q'] . '%';
        }

        $sql = 'SELECT p.id, p.id_lowongan, p.kode_tracking, p.posisi_dilamar, p.penempatan,
                       p.nama_lengkap, p.email, p.no_hp, p.jenis_kelamin, p.foto_3x4,
                       p.pendidikan_terakhir, p.nama_institusi, p.jurusan,
                       p.status_lamaran, p.created_at, p.updated_at,
                       l.judul AS judul_lowongan
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
            'SELECT p.*, l.judul AS judul_lowongan
             FROM pelamar p LEFT JOIN lowongan l ON l.id=p.id_lowongan
             WHERE p.id=:id'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) sendResponse(404, 'Pelamar tidak ditemukan');

        // Decode JSON fields
        $row['kemampuan_komputer'] = json_decode($row['kemampuan_komputer'] ?? '[]', true) ?: [];
        $row['sim']                = json_decode($row['sim'] ?? '[]', true) ?: [];
        $row['khusus_it_bidang']   = json_decode($row['khusus_it_bidang'] ?? '[]', true) ?: [];

        // Get pengalaman
        $stmtP = $this->pdo->prepare('SELECT * FROM pelamar_pengalaman WHERE id_pelamar=:id ORDER BY id');
        $stmtP->execute([':id' => $id]);
        $row['pengalaman'] = $stmtP->fetchAll();

        // Get dokumen
        $stmtD = $this->pdo->prepare('SELECT * FROM pelamar_dokumen WHERE id_pelamar=:id ORDER BY id');
        $stmtD->execute([':id' => $id]);
        $row['dokumen'] = $stmtD->fetchAll();

        sendResponse(200, 'Detail pelamar', $row);
    }

    /** PUT /api/pelamar/{id}/status (admin) */
    public function updateStatus(int $id, array $data): void {
        $allowed = ['pending','review','tes_administrasi','tes_tertulis','interview','diterima','ditolak'];
        $status  = $data['status_lamaran'] ?? '';
        if (!in_array($status, $allowed, true)) {
            sendResponse(400, 'Status tidak valid');
        }
        
        // Get pelamar data for email
        $pelamar = $this->pdo->prepare('SELECT nama_lengkap, email, posisi_dilamar, kode_tracking FROM pelamar WHERE id=:id');
        $pelamar->execute([':id' => $id]);
        $p = $pelamar->fetch();
        if (!$p) sendResponse(404, 'Pelamar tidak ditemukan');
        
        $stmt = $this->pdo->prepare(
            'UPDATE pelamar SET status_lamaran=:s, catatan_admin=:c WHERE id=:id'
        );
        $stmt->execute([
            ':s'  => $status,
            ':c'  => $data['catatan_admin'] ?? null,
            ':id' => $id,
        ]);
        
        // TODO: Kirim email notifikasi perubahan status (uncomment setelah composer install + setup SMTP)
        // $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        // $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
        // $scriptDir = dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
        // $baseUrl  = $protocol . '://' . $host . rtrim($scriptDir, '/');
        // sendStatusUpdateEmail($p['email'], $p['nama_lengkap'], $p['kode_tracking'], $p['posisi_dilamar'], $status, $data['catatan_admin'] ?? '', $baseUrl);
        
        sendResponse(200, 'Status pelamar diperbarui');
    }

    /** DELETE /api/pelamar/{id} (admin) */
    public function destroy(int $id, array $data = []): void {
        // Validasi kode konfirmasi
        $kode_konfirmasi = trim($data['kode_konfirmasi'] ?? '');
        if ($kode_konfirmasi !== 'bkkbisa') {
            sendResponse(403, 'Kode konfirmasi salah. Masukkan kode yang benar untuk menghapus data.');
        }

        $q = $this->pdo->prepare('SELECT foto_3x4, foto_full_body FROM pelamar WHERE id=:id');
        $q->execute([':id' => $id]);
        $row = $q->fetch();
        if (!$row) sendResponse(404, 'Pelamar tidak ditemukan');

        // Hapus file foto
        if (!empty($row['foto_3x4']))      safeDeleteFile($this->uploadRoot . '/foto/' . $row['foto_3x4']);
        if (!empty($row['foto_full_body'])) safeDeleteFile($this->uploadRoot . '/foto/' . $row['foto_full_body']);

        // Hapus file dokumen
        $docs = $this->pdo->prepare('SELECT nama_file FROM pelamar_dokumen WHERE id_pelamar=:id');
        $docs->execute([':id' => $id]);
        foreach ($docs->fetchAll() as $d) {
            safeDeleteFile($this->uploadRoot . '/cv/' . $d['nama_file']);
        }

        // CASCADE akan hapus pengalaman & dokumen
        $this->pdo->prepare('DELETE FROM pelamar WHERE id=:id')->execute([':id' => $id]);
        sendResponse(200, 'Pelamar berhasil dihapus');
    }

    /** GET /api/pelamar/track/{kode} (public) */
    public function track(string $kode): void {
        $stmt = $this->pdo->prepare(
            'SELECT p.kode_tracking, p.nama_lengkap, p.email, p.posisi_dilamar, p.penempatan,
                    p.status_lamaran, p.catatan_admin, p.created_at, p.updated_at,
                    l.judul AS judul_lowongan
             FROM pelamar p LEFT JOIN lowongan l ON l.id = p.id_lowongan
             WHERE p.kode_tracking = :k'
        );
        $stmt->execute([':k' => strtoupper(trim($kode))]);
        $row = $stmt->fetch();
        if (!$row) sendResponse(404, 'Kode tracking tidak ditemukan');
        sendResponse(200, 'Status lamaran', $row);
    }

    /**
     * Validasi value enum - return null jika tidak valid
     */
    private static function validateEnum(?string $value, array $allowed): ?string {
        if ($value === null || $value === '') return null;
        // Coba exact match dulu
        if (in_array($value, $allowed, true)) return $value;
        // Coba case-insensitive match
        foreach ($allowed as $opt) {
            if (strcasecmp($value, $opt) === 0) return $opt;
        }
        // Coba mapping umum
        $map = [
            'belum_menikah' => 'Belum Menikah',
            'belum menikah' => 'Belum Menikah',
            'lajang' => 'Belum Menikah',
            'single' => 'Belum Menikah',
            'menikah' => 'Menikah',
            'kawin' => 'Menikah',
            'married' => 'Menikah',
            'cerai' => 'Cerai',
            'cerai_hidup' => 'Cerai',
            'cerai_mati' => 'Cerai',
            'divorced' => 'Cerai',
        ];
        $lower = strtolower(trim($value));
        return $map[$lower] ?? null;
    }
}
