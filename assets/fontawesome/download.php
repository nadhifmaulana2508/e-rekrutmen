<?php
/**
 * Script untuk download Font Awesome 6.5.1 secara lokal.
 * Jalankan: php assets/fontawesome/download.php
 * 
 * Ini mengatasi masalah "kotak-kotak" icon pada iPhone/iOS Safari
 * yang disebabkan oleh CDN + crossorigin issue.
 */

$version = '6.5.1';
$baseDir = __DIR__;
$zipUrl  = "https://use.fontawesome.com/releases/v{$version}/fontawesome-free-{$version}-web.zip";
$tmpZip  = sys_get_temp_dir() . '/fontawesome.zip';
$tmpDir  = sys_get_temp_dir() . '/fontawesome-extract';

echo "Downloading Font Awesome {$version}...\n";

// Download
$content = file_get_contents($zipUrl);
if ($content === false) {
    die("ERROR: Gagal download dari {$zipUrl}\n");
}
file_put_contents($tmpZip, $content);
echo "Downloaded: " . round(strlen($content) / 1024) . " KB\n";

// Extract
@mkdir($tmpDir, 0755, true);
$zip = new ZipArchive();
if ($zip->open($tmpZip) !== true) {
    die("ERROR: Gagal extract zip\n");
}
$zip->extractTo($tmpDir);
$zip->close();

$srcDir = "{$tmpDir}/fontawesome-free-{$version}-web";

// Copy CSS
@mkdir("{$baseDir}/css", 0755, true);
$cssFiles = ['all.min.css', 'fontawesome.min.css', 'solid.min.css', 'regular.min.css', 'brands.min.css'];
foreach ($cssFiles as $f) {
    $src = "{$srcDir}/css/{$f}";
    if (file_exists($src)) {
        copy($src, "{$baseDir}/css/{$f}");
        echo "  CSS: {$f}\n";
    }
}

// Copy webfonts
@mkdir("{$baseDir}/webfonts", 0755, true);
$fonts = glob("{$srcDir}/webfonts/*");
foreach ($fonts as $font) {
    $name = basename($font);
    copy($font, "{$baseDir}/webfonts/{$name}");
}
echo "  Webfonts: " . count($fonts) . " files copied\n";

// Cleanup
unlink($tmpZip);
array_map('unlink', glob("{$tmpDir}/**/*"));
@rmdir($tmpDir);

echo "\nDone! Font Awesome {$version} tersimpan di assets/fontawesome/\n";
echo "Pastikan webfonts/ ada di ../webfonts/ relatif terhadap css/\n";
