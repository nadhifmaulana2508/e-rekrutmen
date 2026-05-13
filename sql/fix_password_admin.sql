-- ===================================================
-- Quick fix: set password admin default = "admin123"
-- ===================================================
-- Jalankan ini jika Anda sudah import schema versi lama
-- dan login admin gagal karena hash password tidak valid.
--
-- Cara pakai:
--   mysql -u root -p rekrutmen_db < sql/fix_password_admin.sql
-- atau copy-paste ke phpMyAdmin.
-- ===================================================

UPDATE `admin`
SET `password` = '$2y$12$shGa72o4OfADCICf.tbk.ebnZWADl5UW1GuJQfwoQs/yd5CYsjPwO'
WHERE `username` = 'admin';

-- Jika belum ada row admin sama sekali, uncomment INSERT di bawah:
-- INSERT IGNORE INTO `admin` (`username`, `password`, `nama`, `email`, `role`) VALUES
-- ('admin', '$2y$12$shGa72o4OfADCICf.tbk.ebnZWADl5UW1GuJQfwoQs/yd5CYsjPwO', 'Super Administrator', 'admin@rekrutmen.test', 'superadmin');

SELECT 'OK: Login sekarang admin / admin123' AS pesan;
