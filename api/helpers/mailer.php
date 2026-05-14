<?php
/**
 * Email helper menggunakan PHPMailer (SMTP).
 * 
 * Setup:
 * 1. Jalankan: composer install (di root project)
 * 2. Isi konfigurasi SMTP di file .env:
 *    SMTP_HOST=smtp.gmail.com
 *    SMTP_PORT=587
 *    SMTP_USER=email@gmail.com
 *    SMTP_PASS=app-password-16-digit
 *    SMTP_FROM_NAME=HR BPR BKK Jateng
 *
 * Jika PHPMailer belum terinstall, fallback ke PHP mail() (mungkin tidak terkirim di local).
 */

function getMailer() {
    $autoload = __DIR__ . '/../../vendor/autoload.php';
    if (!file_exists($autoload)) return null;
    
    require_once $autoload;
    
    // Load SMTP config from .env
    $envFile = __DIR__ . '/../../.env';
    $env = [];
    if (is_file($envFile)) {
        $env = parse_ini_file($envFile, false, INI_SCANNER_RAW) ?: [];
    }
    
    $host     = $env['SMTP_HOST']      ?? 'smtp.gmail.com';
    $port     = (int)($env['SMTP_PORT'] ?? 587);
    $user     = $env['SMTP_USER']      ?? '';
    $pass     = $env['SMTP_PASS']      ?? '';
    $fromName = $env['SMTP_FROM_NAME'] ?? 'HR BPR BKK Jateng';
    
    if (empty($user) || empty($pass)) return null;
    
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = $host;
    $mail->SMTPAuth   = true;
    $mail->Username   = $user;
    $mail->Password   = $pass;
    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $port;
    $mail->setFrom($user, $fromName);
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    
    return $mail;
}

function buildEmailTemplate(string $namaLengkap, string $kodeTracking, string $posisi, string $baseUrl, string $subject = ''): array {
    $body = "
    <html><head><style>
        body{font-family:'Segoe UI',sans-serif;background:#f1f5f9;padding:20px;margin:0}
        .card{max-width:600px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,0.05)}
        .header{background:linear-gradient(135deg,#6366f1,#8b5cf6,#ec4899);padding:30px;text-align:center;color:#fff}
        .content{padding:30px}
        .kode{background:#f8fafc;border:2px dashed #cbd5e1;border-radius:12px;padding:20px;text-align:center;margin:20px 0}
        .kode h2{font-size:24px;color:#6366f1;letter-spacing:3px;margin:0}
        .btn{display:inline-block;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;padding:12px 24px;border-radius:10px;text-decoration:none;font-weight:bold;margin-top:10px}
        .footer{padding:20px 30px;background:#f8fafc;text-align:center;font-size:12px;color:#94a3b8}
    </style></head><body>
    <div class='card'>
        <div class='header'>
            <h1 style='margin:0;font-size:20px'>PT BPR BKK Jateng (Perseroda)</h1>
            <p style='margin:5px 0 0;opacity:0.8;font-size:14px'>E-Form Rekrutmen Pegawai</p>
        </div>
        <div class='content'>
            <p>Yth. <strong>{$namaLengkap}</strong>,</p>
            <p>Terima kasih telah melamar untuk posisi <strong>{$posisi}</strong> di PT BPR BKK Jateng.</p>
            <p>Lamaran Anda telah kami terima. Berikut adalah kode tracking untuk memantau status lamaran:</p>
            <div class='kode'>
                <p style='margin:0 0 5px;font-size:12px;color:#64748b;font-weight:600'>KODE TRACKING ANDA</p>
                <h2>{$kodeTracking}</h2>
            </div>
            <p>Gunakan kode tersebut untuk mengecek status lamaran di:</p>
            <p style='text-align:center'><a href='{$baseUrl}/status' class='btn'>Cek Status Lamaran</a></p>
            <br>
            <p style='font-size:13px;color:#64748b'><strong>Catatan:</strong></p>
            <ul style='font-size:13px;color:#64748b'>
                <li>Simpan kode tracking ini baik-baik</li>
                <li>Tim HR akan memproses lamaran Anda dalam 2x24 jam kerja</li>
                <li>Jangan membalas email ini</li>
            </ul>
        </div>
        <div class='footer'>
            <p>&copy; " . date('Y') . " PT BPR BKK Jateng (Perseroda). All rights reserved.</p>
        </div>
    </div></body></html>";
    
    return ['subject' => $subject ?: "Lamaran Diterima - Kode Tracking: {$kodeTracking}", 'body' => $body];
}

/**
 * Kirim email kode tracking setelah submit lamaran
 */
function sendTrackingEmail(string $toEmail, string $namaLengkap, string $kodeTracking, string $posisi, string $baseUrl): bool {
    $template = buildEmailTemplate($namaLengkap, $kodeTracking, $posisi, $baseUrl);
    
    try {
        $mail = getMailer();
        if ($mail) {
            $mail->addAddress($toEmail, $namaLengkap);
            $mail->Subject = $template['subject'];
            $mail->Body    = $template['body'];
            $mail->AltBody = strip_tags(str_replace(['<br>', '</p>', '</li>'], ["\n", "\n", "\n"], $template['body']));
            $mail->send();
            return true;
        }
        // Fallback ke PHP mail()
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: HR BPR BKK Jateng <noreply@bprbkkjateng.co.id>\r\n";
        return @mail($toEmail, $template['subject'], $template['body'], $headers);
    } catch (\Throwable $e) {
        error_log("Email gagal ke {$toEmail}: " . $e->getMessage());
        return false;
    }
}

/**
 * Kirim email notifikasi perubahan status lamaran
 */
function sendStatusUpdateEmail(string $toEmail, string $namaLengkap, string $kodeTracking, string $posisi, string $statusBaru, string $catatan, string $baseUrl): bool {
    $statusLabels = [
        'pending'          => 'Menunggu Review',
        'review'           => 'Sedang Direview',
        'tes_administrasi' => 'Lolos ke Tes Administrasi',
        'tes_tertulis'     => 'Lolos ke Tes Tertulis',
        'interview'        => 'Lolos ke Tahap Interview',
        'diterima'         => 'SELAMAT! Anda Diterima',
        'ditolak'          => 'Tidak Lolos Seleksi',
    ];
    $statusLabel = $statusLabels[$statusBaru] ?? $statusBaru;
    $subject = "Update Status Lamaran - {$statusLabel}";
    
    $catatanHtml = $catatan ? "<div style='background:#eef2ff;border:1px solid #c7d2fe;border-radius:8px;padding:15px;margin:15px 0'><p style='margin:0 0 5px;font-size:12px;color:#4338ca;font-weight:bold'>CATATAN DARI TIM HR:</p><p style='margin:0;color:#334155;font-size:14px'>{$catatan}</p></div>" : '';
    
    $statusColor = in_array($statusBaru, ['diterima']) ? '#059669' : (in_array($statusBaru, ['ditolak']) ? '#dc2626' : '#6366f1');
    
    $body = "
    <html><head><style>
        body{font-family:'Segoe UI',sans-serif;background:#f1f5f9;padding:20px;margin:0}
        .card{max-width:600px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,0.05)}
        .header{background:linear-gradient(135deg,#6366f1,#8b5cf6,#ec4899);padding:30px;text-align:center;color:#fff}
        .content{padding:30px}
        .status-badge{display:inline-block;background:{$statusColor};color:#fff;padding:8px 16px;border-radius:8px;font-weight:bold;font-size:14px;margin:10px 0}
        .btn{display:inline-block;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;padding:12px 24px;border-radius:10px;text-decoration:none;font-weight:bold;margin-top:10px}
        .footer{padding:20px 30px;background:#f8fafc;text-align:center;font-size:12px;color:#94a3b8}
    </style></head><body>
    <div class='card'>
        <div class='header'>
            <h1 style='margin:0;font-size:20px'>PT BPR BKK Jateng (Perseroda)</h1>
            <p style='margin:5px 0 0;opacity:0.8;font-size:14px'>Update Status Lamaran</p>
        </div>
        <div class='content'>
            <p>Yth. <strong>{$namaLengkap}</strong>,</p>
            <p>Status lamaran Anda untuk posisi <strong>{$posisi}</strong> telah diperbarui:</p>
            <p style='text-align:center'><span class='status-badge'>{$statusLabel}</span></p>
            <table style='width:100%;font-size:13px;color:#475569;margin:15px 0'>
                <tr><td style='padding:5px 0'><strong>Kode Tracking:</strong></td><td>{$kodeTracking}</td></tr>
                <tr><td style='padding:5px 0'><strong>Posisi:</strong></td><td>{$posisi}</td></tr>
                <tr><td style='padding:5px 0'><strong>Status Terbaru:</strong></td><td>{$statusLabel}</td></tr>
            </table>
            {$catatanHtml}
            <p style='text-align:center'><a href='{$baseUrl}/status' class='btn'>Lihat Detail Status</a></p>
        </div>
        <div class='footer'>
            <p>&copy; " . date('Y') . " PT BPR BKK Jateng (Perseroda). All rights reserved.</p>
        </div>
    </div></body></html>";
    
    try {
        $mail = getMailer();
        if ($mail) {
            $mail->addAddress($toEmail, $namaLengkap);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags(str_replace(['<br>', '</p>', '</li>'], ["\n", "\n", "\n"], $body));
            $mail->send();
            return true;
        }
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: HR BPR BKK Jateng <noreply@bprbkkjateng.co.id>\r\n";
        return @mail($toEmail, $subject, $body, $headers);
    } catch (\Throwable $e) {
        error_log("Email status update gagal ke {$toEmail}: " . $e->getMessage());
        return false;
    }
}
