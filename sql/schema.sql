-- ==========================================================
-- E-FORM REKRUTMEN PEGAWAI - DATABASE SCHEMA
-- ==========================================================
-- Jalankan di MySQL/MariaDB setelah buat database rekrutmen_db
-- ==========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ===== TABEL ADMIN =====
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL COMMENT 'password_hash() bcrypt',
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `role` ENUM('superadmin','admin') NOT NULL DEFAULT 'admin',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed default admin -> username: admin, password: admin123
-- Hash bcrypt dibuat via: password_hash('admin123', PASSWORD_BCRYPT)
INSERT INTO `admin` (`username`, `password`, `nama`, `email`, `role`) VALUES
('admin', '$2y$12$shGa72o4OfADCICf.tbk.ebnZWADl5UW1GuJQfwoQs/yd5CYsjPwO', 'Super Administrator', 'admin@rekrutmen.test', 'superadmin');
-- Login default: admin / admin123
-- Jika butuh reset password, jalankan: php sql/seed.php

-- ===== TABEL LOWONGAN =====
DROP TABLE IF EXISTS `lowongan`;
CREATE TABLE `lowongan` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `judul` VARCHAR(150) NOT NULL,
  `divisi` VARCHAR(100) NOT NULL,
  `lokasi` VARCHAR(150) NOT NULL,
  `tipe_kerja` ENUM('full_time','part_time','kontrak','magang','freelance') NOT NULL DEFAULT 'full_time',
  `level` ENUM('fresh_graduate','junior','middle','senior','lead','manager') NOT NULL DEFAULT 'junior',
  `deskripsi` TEXT NOT NULL,
  `requirements` TEXT NOT NULL COMMENT 'Pisahkan per baris',
  `benefits` TEXT DEFAULT NULL COMMENT 'Pisahkan per baris',
  `gaji_min` BIGINT(20) DEFAULT NULL,
  `gaji_max` BIGINT(20) DEFAULT NULL,
  `deadline` DATE DEFAULT NULL,
  `status` ENUM('aktif','nonaktif','closed') NOT NULL DEFAULT 'aktif',
  `dibuat_oleh` INT(11) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_divisi` (`divisi`),
  CONSTRAINT `fk_lowongan_admin` FOREIGN KEY (`dibuat_oleh`) REFERENCES `admin`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed contoh lowongan (dibuat_oleh = NULL untuk amankan dari FK issue)
INSERT INTO `lowongan` (`judul`,`divisi`,`lokasi`,`tipe_kerja`,`level`,`deskripsi`,`requirements`,`benefits`,`gaji_min`,`gaji_max`,`deadline`,`status`,`dibuat_oleh`) VALUES
('Frontend Developer','Teknologi','Jakarta Selatan','full_time','middle','Bertanggung jawab mengembangkan antarmuka web yang modern, cepat, dan aksesibel untuk produk internal maupun produk pelanggan.','Minimal S1 Teknik Informatika / sederajat\nMenguasai HTML, CSS, JavaScript, dan framework modern (React/Vue)\nPengalaman minimal 2 tahun\nMemahami responsive design & performance tuning\nTerbiasa dengan Git workflow','BPJS Kesehatan & Ketenagakerjaan\nTunjangan makan & transport\nBonus tahunan\nRemote friendly (hybrid)\nLaptop disediakan',8000000,15000000,'2026-07-31','aktif',NULL),
('Backend Engineer (PHP)','Teknologi','Semarang','full_time','senior','Membangun dan memelihara REST API, mengoptimalkan query database, serta melakukan code review untuk tim junior.','Pengalaman min. 4 tahun PHP (Laravel/Symfony/Native)\nMahir MySQL/PostgreSQL, indexing & query optimization\nPaham konsep OOP, SOLID, Design Patterns\nTerbiasa dengan CI/CD (GitHub Actions/GitLab CI)\nNilai plus: Docker, Redis, RabbitMQ',"BPJS Kesehatan & Ketenagakerjaan\nGaji kompetitif\nWFH 3 hari/minggu\nAllowance pelatihan/sertifikasi\nMakan siang gratis",12000000,22000000,'2026-06-30','aktif',NULL),
('HR Recruiter','Human Resources','Jakarta Selatan','full_time','junior','Mengelola end-to-end proses rekrutmen mulai dari sourcing, screening, hingga onboarding kandidat.','Minimal S1 Psikologi / Manajemen SDM\nPengalaman min. 1 tahun di bidang rekrutmen\nMemahami teknik interview behavioral\nKomunikatif dan detail-oriented\nTerbiasa menggunakan ATS (Applicant Tracking System)','BPJS Kesehatan & Ketenagakerjaan\nKomisi per kandidat onboard\nTraining HR profesional\nCuti tahunan 14 hari',6000000,9000000,'2026-06-15','aktif',NULL),
('UI/UX Designer','Desain','Remote','kontrak','middle','Merancang pengalaman pengguna untuk produk digital perusahaan serta membangun dan menjaga design system.','Portfolio yang kuat (Dribbble/Behance/Figma)\nMenguasai Figma, desain sistem, prototyping\nPaham prinsip accessibility & usability\nPengalaman min. 2 tahun\nNilai plus: motion design (After Effects/Rive)','Fully remote\nKontrak 12 bulan (extendable)\nAllowance hardware\nMacBook Pro disediakan',10000000,16000000,'2026-07-15','aktif',NULL),
('Management Trainee','Operasional','Surabaya','full_time','fresh_graduate','Program pengembangan pemimpin masa depan. Rotasi ke beberapa divisi selama 18 bulan.','S1 semua jurusan, IPK min 3.25\nFresh graduate atau pengalaman maks 1 tahun\nUsia maksimal 25 tahun\nLeadership, komunikasi, dan analytical thinking baik\nBersedia ditempatkan di seluruh Indonesia','Gaji pokok + tunjangan program\nMentor 1-on-1\nFast track career path\nBPJS & asuransi kesehatan swasta\nDinas ke luar kota',7500000,10000000,'2026-06-30','aktif',NULL);

-- ===== TABEL PELAMAR =====
DROP TABLE IF EXISTS `pelamar`;
CREATE TABLE `pelamar` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_lowongan` INT(11) NOT NULL,
  `kode_tracking` VARCHAR(32) NOT NULL UNIQUE COMMENT 'Kode unik untuk cek status publik',
  `nama_lengkap` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `telepon` VARCHAR(30) NOT NULL,
  `tempat_lahir` VARCHAR(100) DEFAULT NULL,
  `tanggal_lahir` DATE DEFAULT NULL,
  `jenis_kelamin` ENUM('L','P') DEFAULT NULL,
  `alamat` TEXT DEFAULT NULL,
  `pendidikan_terakhir` ENUM('SMA','SMK','D3','D4','S1','S2','S3') DEFAULT NULL,
  `nama_institusi` VARCHAR(150) DEFAULT NULL,
  `jurusan` VARCHAR(150) DEFAULT NULL,
  `tahun_lulus` YEAR DEFAULT NULL,
  `ipk` DECIMAL(3,2) DEFAULT NULL,
  `pengalaman_kerja` TEXT DEFAULT NULL,
  `link_portfolio` VARCHAR(255) DEFAULT NULL,
  `foto` VARCHAR(255) DEFAULT NULL,
  `cv` VARCHAR(255) DEFAULT NULL,
  `status_lamaran` ENUM('pending','review','interview','diterima','ditolak') NOT NULL DEFAULT 'pending',
  `catatan_admin` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status_lamaran`),
  KEY `idx_lowongan` (`id_lowongan`),
  CONSTRAINT `fk_pelamar_lowongan` FOREIGN KEY (`id_lowongan`) REFERENCES `lowongan`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
