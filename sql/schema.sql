-- ==========================================================
-- E-FORM REKRUTMEN PEGAWAI - PT BPR BKK JATENG (Perseroda)
-- ==========================================================
-- Database: rekrutmen_db
-- Drop semua & buat ulang
-- ==========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ===== TABEL ADMIN =====
DROP TABLE IF EXISTS `pelamar_dokumen`;
DROP TABLE IF EXISTS `pelamar_pengalaman`;
DROP TABLE IF EXISTS `pelamar`;
DROP TABLE IF EXISTS `lowongan`;
DROP TABLE IF EXISTS `admin`;

CREATE TABLE `admin` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `role` ENUM('superadmin','admin','hr') NOT NULL DEFAULT 'admin',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default: admin / admin123
INSERT INTO `admin` (`username`, `password`, `nama`, `email`, `role`) VALUES
('admin', '$2y$12$shGa72o4OfADCICf.tbk.ebnZWADl5UW1GuJQfwoQs/yd5CYsjPwO', 'Super Administrator', 'admin@bprbkkjateng.co.id', 'superadmin');

-- ===== TABEL LOWONGAN =====
CREATE TABLE `lowongan` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `judul` VARCHAR(200) NOT NULL COMMENT 'Nama lowongan / batch',
  `deskripsi` TEXT DEFAULT NULL,
  `persyaratan` TEXT DEFAULT NULL COMMENT 'Persyaratan umum, pisahkan per baris',
  `posisi_tersedia` TEXT NOT NULL COMMENT 'JSON array posisi yang dibuka',
  `penempatan_tersedia` TEXT NOT NULL COMMENT 'JSON array lokasi penempatan',
  `deadline` DATE DEFAULT NULL,
  `status` ENUM('aktif','nonaktif','closed') NOT NULL DEFAULT 'aktif',
  `dibuat_oleh` INT(11) DEFAULT NULL,
  `dibuat_oleh_nama` VARCHAR(150) DEFAULT NULL COMMENT 'Nama pembuat lowongan (admin lokal atau SSO user)',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_lowongan_admin` FOREIGN KEY (`dibuat_oleh`) REFERENCES `admin`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed lowongan default
INSERT INTO `lowongan` (`judul`, `deskripsi`, `persyaratan`, `posisi_tersedia`, `penempatan_tersedia`, `deadline`, `status`, `dibuat_oleh`, `dibuat_oleh_nama`) VALUES
('Rekrutmen Pegawai BPR BKK Jateng Tahun 2026',
 'PT BPR BKK Jateng (Perseroda) membuka kesempatan bagi putra-putri terbaik Jawa Tengah untuk bergabung sebagai pegawai tetap. Kami mencari individu yang berintegritas, profesional, dan siap berkontribusi untuk kemajuan perbankan daerah.',
 'Warga Negara Indonesia\nUsia maksimal 27 tahun per tanggal penutupan\nPendidikan minimal D3/S1 (sesuai posisi)\nIPK minimal 2.75 dari skala 4.00\nSehat jasmani dan rohani\nBerkelakuan baik (SKCK)\nTidak memiliki hubungan keluarga dengan Direksi/Komisaris\nBersedia ditempatkan di seluruh wilayah kerja perusahaan\nTidak pernah diberhentikan tidak hormat dari instansi lain',
 '["AO Dana","AO Kredit","AO Remedial","Analis Kredit & Appraisal","Akuntansi & Pelaporan","Customer Service","Teller","Staf Manajemen Risiko","Staf Kepatuhan","Staf Strategi Anti Fraud","Staf Perlindungan Konsumen","Staf Integritas Pelaporan Keuangan","Staf APU-PPT","Staf Digital Marketing","Staf IT (Development/Security)","Staf Litbang","Staf Penyelesaian Kredit","Staf AMU dan Litigasi","Staf Diklat"]',
 '["Cabang Utama (Kota Semarang)","Rembang","Pati","Demak","Kendal","Kota Salatiga","Kab. Semarang","Wonogiri","Kota Surakarta","Karanganyar","Sukoharjo","Sragen","Boyolali","Magelang","Wonosobo","Purworejo","Kebumen","Banjarnegara","Purbalingga","Banyumas","Cilacap","Kab. Tegal","Brebes","Kota Tegal","Pemalang","Kota Pekalongan","Kab. Pekalongan","Batang"]',
 '2026-07-31', 'aktif', 1, 'Super Administrator');

-- ===== TABEL PELAMAR =====
CREATE TABLE `pelamar` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_lowongan` INT(11) NOT NULL,
  `kode_tracking` VARCHAR(32) NOT NULL UNIQUE,
  
  -- A. POSISI YANG DILAMAR
  `posisi_dilamar` VARCHAR(150) NOT NULL,
  `penempatan` VARCHAR(150) NOT NULL,
  `bersedia_seluruh_wilayah` ENUM('Ya','Tidak') NOT NULL DEFAULT 'Ya',
  `sumber_informasi` VARCHAR(100) DEFAULT NULL,
  `sumber_informasi_lainnya` VARCHAR(150) DEFAULT NULL,
  `ekspektasi_gaji` BIGINT(20) DEFAULT NULL,
  `ketersediaan_mulai` VARCHAR(50) DEFAULT NULL,
  `ketersediaan_mulai_lainnya` VARCHAR(100) DEFAULT NULL,
  
  -- B. DATA PRIBADI
  `nama_lengkap` VARCHAR(150) NOT NULL,
  `nama_panggilan` VARCHAR(50) DEFAULT NULL,
  `jenis_kelamin` ENUM('Laki-laki','Perempuan') NOT NULL,
  `tempat_lahir` VARCHAR(100) NOT NULL,
  `tanggal_lahir` DATE NOT NULL,
  `status_pernikahan` ENUM('Belum Menikah','Menikah','Cerai') DEFAULT NULL,
  `agama` VARCHAR(30) DEFAULT NULL,
  `kewarganegaraan` VARCHAR(50) DEFAULT 'Indonesia',
  `nomor_ktp` VARCHAR(20) NOT NULL,
  `npwp` VARCHAR(30) DEFAULT NULL,
  `alamat_ktp` TEXT NOT NULL,
  `alamat_domisili` TEXT NOT NULL,
  `no_hp` VARCHAR(20) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `akun_linkedin` VARCHAR(255) DEFAULT NULL,
  `foto_3x4` VARCHAR(255) DEFAULT NULL,
  `foto_full_body` VARCHAR(255) DEFAULT NULL,
  
  -- C. PENDIDIKAN
  `pendidikan_terakhir` VARCHAR(30) NOT NULL,
  `pendidikan_lainnya` VARCHAR(100) DEFAULT NULL,
  `nama_institusi` VARCHAR(200) NOT NULL,
  `jurusan` VARCHAR(150) NOT NULL,
  `tahun_masuk` YEAR DEFAULT NULL,
  `tahun_lulus` YEAR NOT NULL,
  `ipk` VARCHAR(10) DEFAULT NULL,
  `prestasi` TEXT DEFAULT NULL,
  
  -- E. KEMAMPUAN & KOMPETENSI
  `kemampuan_komputer` TEXT DEFAULT NULL COMMENT 'JSON array',
  `kemampuan_komputer_lainnya` VARCHAR(200) DEFAULT NULL,
  `bahasa_asing` TEXT DEFAULT NULL,
  `sertifikasi` TEXT DEFAULT NULL,
  `keahlian_khusus` TEXT DEFAULT NULL,
  `sim` TEXT DEFAULT NULL COMMENT 'JSON array: SIM A, SIM C, Tidak Ada',
  
  -- F. KHUSUS POSISI TERTENTU
  `khusus_pengalaman_marketing` ENUM('Ya','Tidak') DEFAULT NULL,
  `khusus_relasi_bisnis` ENUM('Ya','Tidak') DEFAULT NULL,
  `khusus_bersedia_target` ENUM('Ya','Tidak') DEFAULT NULL,
  `khusus_kendaraan_pribadi` ENUM('Ya','Tidak') DEFAULT NULL,
  `khusus_it_bidang` TEXT DEFAULT NULL COMMENT 'JSON array',
  `khusus_it_bahasa_pemrograman` TEXT DEFAULT NULL,
  `khusus_analis_lapkeu` ENUM('Ya','Tidak') DEFAULT NULL,
  `khusus_analis_appraisal` ENUM('Ya','Tidak') DEFAULT NULL,
  
  -- G. INFORMASI TAMBAHAN
  `pernah_kasus_hukum` ENUM('Ya','Tidak') NOT NULL DEFAULT 'Tidak',
  `hubungan_keluarga_pegawai` ENUM('Ya','Tidak') NOT NULL DEFAULT 'Tidak',
  `hubungan_keluarga_detail` VARCHAR(200) DEFAULT NULL,
  `status_slik` ENUM('Lancar','Tidak Lancar') DEFAULT NULL,
  `riwayat_penyakit` ENUM('Ya','Tidak') DEFAULT NULL,
  `riwayat_penyakit_detail` TEXT DEFAULT NULL,
  
  -- I. PERNYATAAN
  `pernyataan_data_benar` TINYINT(1) NOT NULL DEFAULT 0,
  `pernyataan_ikut_proses` TINYINT(1) NOT NULL DEFAULT 0,
  `pernyataan_setuju_data` TINYINT(1) NOT NULL DEFAULT 0,
  `tanggal_pengisian` DATE DEFAULT NULL,
  `tanda_tangan` VARCHAR(255) DEFAULT NULL COMMENT 'Nama pelamar sebagai tanda tangan digital',
  
  -- STATUS
  `status_lamaran` ENUM('pending','review','tes_administrasi','tes_tertulis','interview','diterima','ditolak') NOT NULL DEFAULT 'pending',
  `catatan_admin` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status_lamaran`),
  KEY `idx_lowongan` (`id_lowongan`),
  KEY `idx_email` (`email`),
  CONSTRAINT `fk_pelamar_lowongan` FOREIGN KEY (`id_lowongan`) REFERENCES `lowongan`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===== TABEL PENGALAMAN KERJA (multiple) =====
CREATE TABLE `pelamar_pengalaman` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_pelamar` INT(11) NOT NULL,
  `nama_perusahaan` VARCHAR(200) NOT NULL,
  `jabatan` VARCHAR(150) DEFAULT NULL,
  `periode_kerja` VARCHAR(100) DEFAULT NULL,
  `deskripsi_pekerjaan` TEXT DEFAULT NULL,
  `gaji_terakhir` BIGINT(20) DEFAULT NULL,
  `alasan_berhenti` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pelamar` (`id_pelamar`),
  CONSTRAINT `fk_pengalaman_pelamar` FOREIGN KEY (`id_pelamar`) REFERENCES `pelamar`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===== TABEL DOKUMEN UPLOAD =====
CREATE TABLE `pelamar_dokumen` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_pelamar` INT(11) NOT NULL,
  `jenis_dokumen` VARCHAR(100) NOT NULL COMMENT 'surat_lamaran, cv, ktp, kk, ijazah, transkrip, surat_sehat, foto_3x4, foto_fullbody, sertifikat, surat_kerja, portfolio',
  `nama_file` VARCHAR(255) NOT NULL,
  `nama_asli` VARCHAR(255) DEFAULT NULL,
  `ukuran` INT(11) DEFAULT NULL COMMENT 'bytes',
  `uploaded_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pelamar` (`id_pelamar`),
  CONSTRAINT `fk_dokumen_pelamar` FOREIGN KEY (`id_pelamar`) REFERENCES `pelamar`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
