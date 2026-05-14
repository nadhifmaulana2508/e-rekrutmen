<?php
/**
 * Upload helper dengan defensive try/catch + security hardening.
 * - MIME type validation via finfo (not just extension)
 * - File content scanning (no PHP/script injection)
 * - Extension whitelist check
 * - Size limits enforced
 * - Random filenames to prevent path traversal
 */

require_once __DIR__ . '/../middlewares/securityMiddleware.php';

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

    // Validate extension from original filename
    $originalName = $fileInfo['name'] ?? '';
    if (!validateFileExtension($originalName, ['jpg', 'jpeg', 'png', 'webp'])) {
        sendResponse(415, 'Ekstensi foto harus .jpg, .jpeg, .png, atau .webp');
    }

    // Validasi MIME via finfo (more reliable than mime_content_type)
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($fileInfo['tmp_name']);
    if (!isset($allowed[$mime])) {
        sendResponse(415, 'Format foto harus JPG, PNG, atau WEBP (terdeteksi: ' . $mime . ')');
    }

    // Check file is actually an image (getimagesize)
    $imageInfo = @getimagesize($fileInfo['tmp_name']);
    if ($imageInfo === false) {
        sendResponse(415, 'File bukan gambar yang valid');
    }

    // Scan for embedded scripts/PHP
    if (!validateFileContent($fileInfo['tmp_name'])) {
        sendResponse(415, 'File foto mengandung konten berbahaya');
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

    // Validate extension
    $originalName = $fileInfo['name'] ?? '';
    if (!validateFileExtension($originalName, ['pdf'])) {
        sendResponse(415, 'CV harus berformat PDF (.pdf)');
    }

    // Validate MIME via finfo
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($fileInfo['tmp_name']);
    if ($mime !== 'application/pdf') {
        sendResponse(415, 'CV harus berformat PDF (terdeteksi: ' . $mime . ')');
    }

    // Scan for embedded scripts
    if (!validateFileContent($fileInfo['tmp_name'])) {
        sendResponse(415, 'File CV mengandung konten berbahaya');
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
 * Upload dokumen (PDF/gambar, maks 5 MB)
 */
function uploadDokumen(array $fileInfo, string $dir, string $prefix = 'doc'): ?string {
    if (empty($fileInfo['tmp_name']) || ($fileInfo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    if (($fileInfo['size'] ?? 0) > 5 * 1024 * 1024) {
        sendResponse(413, 'Ukuran dokumen maksimal 5MB');
    }

    // Validate extension from original filename
    $originalName = $fileInfo['name'] ?? '';
    if (!validateFileExtension($originalName, ['pdf', 'jpg', 'jpeg', 'png'])) {
        sendResponse(415, "Ekstensi dokumen '{$prefix}' harus .pdf, .jpg, .jpeg, atau .png");
    }

    // Validate MIME type via finfo
    $allowedMime = [
        'application/pdf' => 'pdf',
        'image/jpeg'      => 'jpg',
        'image/png'       => 'png',
    ];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($fileInfo['tmp_name']);
    if (!isset($allowedMime[$mime])) {
        sendResponse(415, "Format dokumen '{$prefix}' harus PDF, JPG, atau PNG (terdeteksi: {$mime})");
    }

    // If it claims to be an image, verify it actually is
    if (strpos($mime, 'image/') === 0) {
        $imageInfo = @getimagesize($fileInfo['tmp_name']);
        if ($imageInfo === false) {
            sendResponse(415, "File dokumen '{$prefix}' bukan gambar yang valid");
        }
    }

    // Scan for embedded scripts/PHP in all files
    if (!validateFileContent($fileInfo['tmp_name'])) {
        sendResponse(415, "File dokumen '{$prefix}' mengandung konten berbahaya");
    }

    try {
        if (!is_dir($dir)) @mkdir($dir, 0755, true);
        if (!is_writable($dir)) {
            sendResponse(500, 'Folder upload tidak dapat ditulis');
        }

        $ext      = $allowedMime[$mime];
        $filename = $prefix . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $target   = rtrim($dir, '/') . '/' . $filename;

        if (!move_uploaded_file($fileInfo['tmp_name'], $target)) {
            sendResponse(500, 'Gagal memindahkan dokumen ke server');
        }
        return $filename;
    } catch (Throwable $e) {
        sendResponse(500, 'Upload dokumen error: ' . $e->getMessage());
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
