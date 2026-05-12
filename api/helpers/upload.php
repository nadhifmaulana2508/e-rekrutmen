<?php
/**
 * Upload helper dengan defensive try/catch.
 * Pola dari PR #11 & #16 project lelang: jangan sampai crash karena
 * konfigurasi server yang beda-beda (permission, ekstensi, dll).
 */

/**
 * Upload gambar (foto pelamar). Disimpan apa adanya (jpg/png/webp).
 * Maks 2 MB.
 *
 * @param array $fileInfo  item dari $_FILES['foto']
 * @param string $dir      folder tujuan (path absolut)
 * @param string $prefix   prefix nama file
 * @return string|null     nama file hasil upload, atau null jika tidak ada/gagal
 */
function uploadFoto(array $fileInfo, string $dir, string $prefix = 'foto'): ?string {
    if (empty($fileInfo['tmp_name']) || ($fileInfo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    // Validasi size (max 2MB)
    if (($fileInfo['size'] ?? 0) > 2 * 1024 * 1024) {
        sendResponse(413, 'Ukuran foto maksimal 2MB');
    }

    // Validasi mime
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime    = mime_content_type($fileInfo['tmp_name']) ?: '';
    if (!isset($allowed[$mime])) {
        sendResponse(415, 'Format foto harus JPG, PNG, atau WEBP');
    }

    try {
        if (!is_dir($dir)) @mkdir($dir, 0755, true);
        if (!is_writable($dir)) {
            sendResponse(500, 'Folder upload foto tidak dapat ditulis. Periksa permission di: ' . $dir);
        }

        $ext      = $allowed[$mime];
        $filename = $prefix . '_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
        $target   = rtrim($dir, '/') . '/' . $filename;

        if (!move_uploaded_file($fileInfo['tmp_name'], $target)) {
            sendResponse(500, 'Gagal memindahkan file foto ke server');
        }
        return $filename;
    } catch (Throwable $e) {
        sendResponse(500, 'Upload foto error: ' . $e->getMessage());
    }
    return null;
}

/**
 * Upload CV (hanya PDF, maks 3 MB)
 */
function uploadCv(array $fileInfo, string $dir, string $prefix = 'cv'): ?string {
    if (empty($fileInfo['tmp_name']) || ($fileInfo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    if (($fileInfo['size'] ?? 0) > 3 * 1024 * 1024) {
        sendResponse(413, 'Ukuran CV maksimal 3MB');
    }

    $mime = mime_content_type($fileInfo['tmp_name']) ?: '';
    if ($mime !== 'application/pdf') {
        sendResponse(415, 'CV harus berformat PDF');
    }

    try {
        if (!is_dir($dir)) @mkdir($dir, 0755, true);
        if (!is_writable($dir)) {
            sendResponse(500, 'Folder upload CV tidak dapat ditulis. Periksa permission di: ' . $dir);
        }

        $filename = $prefix . '_' . time() . '_' . bin2hex(random_bytes(3)) . '.pdf';
        $target   = rtrim($dir, '/') . '/' . $filename;

        if (!move_uploaded_file($fileInfo['tmp_name'], $target)) {
            sendResponse(500, 'Gagal memindahkan file CV ke server');
        }
        return $filename;
    } catch (Throwable $e) {
        sendResponse(500, 'Upload CV error: ' . $e->getMessage());
    }
    return null;
}

/**
 * Hapus file dengan aman. Return true jika file tidak ada / berhasil dihapus.
 */
function safeDeleteFile(string $path): bool {
    if (!is_file($path)) return true;
    return @unlink($path);
}
