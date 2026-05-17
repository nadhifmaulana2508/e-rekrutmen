-- ============================================================
-- Migration: Tambah kolom dibuat_oleh_nama di tabel lowongan
-- Tujuan: Menyimpan nama pembuat lowongan (termasuk SSO user)
--         karena SSO user tidak memiliki ID di tabel admin
-- ============================================================

ALTER TABLE `lowongan` 
ADD COLUMN `dibuat_oleh_nama` VARCHAR(150) DEFAULT NULL 
COMMENT 'Nama pembuat lowongan (admin lokal atau SSO user)' 
AFTER `dibuat_oleh`;

-- Update existing data: isi nama dari tabel admin jika ada
UPDATE `lowongan` l
LEFT JOIN `admin` a ON l.dibuat_oleh = a.id
SET l.dibuat_oleh_nama = a.nama
WHERE l.dibuat_oleh IS NOT NULL AND a.id IS NOT NULL;
