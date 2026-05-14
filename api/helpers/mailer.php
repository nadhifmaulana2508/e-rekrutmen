<?php
/**
 * Simple email sender using PHP mail().
 * Untuk production, sebaiknya gunakan SMTP library (PHPMailer).
 * Di local XAMPP, mail() biasanya tidak terkirim kecuali setup Helo/sendmail.
 */

function sendTrackingEmail(string $toEmail, string $namaLengkap, string $kodeTracking, string $posisi, string $baseUrl): bool {
    $subject = "Lamaran Anda Diterima - Kode Tracking: {$kodeTracking}";
    
    $body = "
    <html>
    <head><style>
        body{font-family:'Segoe UI',sans-serif;background:#f1f5f9;padding:20px}
        .card{max-width:600px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,0.05)}
        .header{background:linear-gradient(135deg,#6366f1,#8b5cf6,#ec4899);padding:30px;text-align:center;color:#fff}
        .content{padding:30px}
        .kode{background:#f8fafc;border:2px dashed #cbd5e1;border-radius:12px;padding:20px;text-align:center;margin:20px 0}
        .kode h2{font-size:24px;color:#6366f1;letter-spacing:3px;margin:0}
        .btn{display:inline-block;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;padding:12px 24px;border-radius:10px;text-decoration:none;font-weight:bold;margin-top:10px}
        .footer{padding:20px 30px;background:#f8fafc;text-align:center;font-size:12px;color:#94a3b8}
    </style></head>
    <body>
    <div class='card'>
        <div class='header'>
            <h1 style='margin:0;font-size:20px'>PT BPR BKK Jateng (Perseroda)</h1>
            <p style='margin:5px 0 0;opacity:0.8;font-size:14px'>E-Form Rekrutmen Pegawai</p>
        </div>
        <div class='content'>
            <p>Yth. <strong>{$namaLengkap}</strong>,</p>
            <p>Terima kasih telah melamar untuk posisi <strong>{$posisi}</strong> di PT BPR BKK Jateng.</p>
            <p>Lamaran Anda telah kami terima. Berikut adalah kode tracking untuk memantau status lamaran Anda:</p>
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
    </div>
    </body></html>";

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: HR BPR BKK Jateng <noreply@bprbkkjateng.co.id>\r\n";
    $headers .= "Reply-To: hr@bprbkkjateng.co.id\r\n";

    // Coba kirim, tapi jangan block proses jika gagal
    try {
        return @mail($toEmail, $subject, $body, $headers);
    } catch (Throwable $e) {
        // Log error tapi jangan block submit lamaran
        error_log("Email gagal kirim ke {$toEmail}: " . $e->getMessage());
        return false;
    }
}
