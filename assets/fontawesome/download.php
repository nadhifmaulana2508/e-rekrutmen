<?php
/**
 * Script untuk download Font Awesome 6.5.1 secara lokal.
 * Jalankan: php assets/fontawesome/download.php
 * 
 * Ini mengatasi masalah "kotak-kotak" icon pada iPhone/iOS Safari
 * yang disebabkan oleh CDN + crossorigin issue.
 * 
 * Support: ZipArchive (PHP ext-zip) ATAU shell unzip command.
 */

$version = '6.5.1';
$baseDir = __DIR__;
$zipUrl  = "https://use.fontawesome.com/releases/v{$version}/fontawesome-free-{$version}-web.zip";
$tmpZip  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'fontawesome.zip';
$tmpDir  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'fontawesome-extract';

echo "Downloading Font Awesome {$version}...\n";

// Download
$content = file_get_contents($zipUrl);
if ($content === false) {
    die("ERROR: Gagal download dari {$zipUrl}\nPastikan koneksi internet aktif.\n");
}
file_put_contents($tmpZip, $content);
echo "Downloaded: " . round(strlen($content) / 1024) . " KB\n";

// Extract - coba ZipArchive dulu, fallback ke shell command
$extractDir = "{$tmpDir}/fontawesome-free-{$version}-web";

if (class_exists('ZipArchive')) {
    echo "Extracting dengan ZipArchive...\n";
    @mkdir($tmpDir, 0755, true);
    $zip = new ZipArchive();
    if ($zip->open($tmpZip) !== true) {
        die("ERROR: Gagal extract zip\n");
    }
    $zip->extractTo($tmpDir);
    $zip->close();
} else {
    echo "ZipArchive tidak tersedia, mencoba shell unzip...\n";
    @mkdir($tmpDir, 0755, true);

    // Coba unzip (Linux/Mac) atau tar (fallback)
    $unzipCmd = '';
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Windows: pakai PowerShell
        $unzipCmd = sprintf(
            'powershell -Command "Expand-Archive -Path %s -DestinationPath %s -Force"',
            escapeshellarg($tmpZip),
            escapeshellarg($tmpDir)
        );
    } else {
        // Linux/Mac
        $unzipCmd = sprintf('unzip -qo %s -d %s', escapeshellarg($tmpZip), escapeshellarg($tmpDir));
    }

    exec($unzipCmd, $output, $exitCode);
    if ($exitCode !== 0) {
        // Fallback: coba 7z
        $cmd7z = sprintf('7z x %s -o%s -y', escapeshellarg($tmpZip), escapeshellarg($tmpDir));
        exec($cmd7z, $output2, $exitCode2);
        if ($exitCode2 !== 0) {
            @unlink($tmpZip);
            die("ERROR: Tidak bisa extract zip.\nInstall salah satu: php-zip extension, unzip, atau 7z.\n");
        }
    }
}

// Verifikasi extract berhasil
if (!is_dir($extractDir)) {
    // Cek apakah ada folder dengan nama berbeda
    $dirs = glob($tmpDir . '/fontawesome-free-*-web');
    if (!empty($dirs)) {
        $extractDir = $dirs[0];
    } else {
        @unlink($tmpZip);
        die("ERROR: Folder hasil extract tidak ditemukan.\n");
    }
}

// Copy CSS
@mkdir("{$baseDir}/css", 0755, true);
$cssFiles = ['all.min.css', 'all.css', 'fontawesome.min.css', 'solid.min.css', 'regular.min.css', 'brands.min.css'];
$cssCopied = 0;
foreach ($cssFiles as $f) {
    $src = "{$extractDir}/css/{$f}";
    if (file_exists($src)) {
        copy($src, "{$baseDir}/css/{$f}");
        echo "  CSS: {$f}\n";
        $cssCopied++;
    }
}

// Copy webfonts
@mkdir("{$baseDir}/webfonts", 0755, true);
$fonts = glob("{$extractDir}/webfonts/*");
foreach ($fonts as $font) {
    $name = basename($font);
    copy($font, "{$baseDir}/webfonts/{$name}");
}
echo "  Webfonts: " . count($fonts) . " files copied\n";

// Cleanup
@unlink($tmpZip);
// Recursive delete temp dir
function rrmdir($dir) {
    if (!is_dir($dir)) return;
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        is_dir($path) ? rrmdir($path) : @unlink($path);
    }
    @rmdir($dir);
}
rrmdir($tmpDir);

echo "\nDone! Font Awesome {$version} tersimpan di assets/fontawesome/\n";
echo "  CSS files: {$cssCopied}\n";
echo "  Webfonts: " . count($fonts) . "\n";
echo "\nPastikan webfonts/ ada di ../webfonts/ relatif terhadap css/\n";
