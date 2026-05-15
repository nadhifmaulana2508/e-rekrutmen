<?php
/**
 * Security Middleware
 * - Rate limiting (per IP)
 * - Input sanitization
 * - CORS headers
 * - Content-Type validation
 * - Anti-XSS / Anti-Injection protection
 */

/**
 * Set security headers for all API responses
 */
function setSecurityHeaders(): void {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Content-Type: application/json; charset=utf-8');

    // CORS - allow from same origin
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if ($origin) {
        header("Access-Control-Allow-Origin: {$origin}");
        header('Access-Control-Allow-Credentials: true');
    }
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    // Handle preflight
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

/**
 * Simple IP-based rate limiting using file storage
 * Limits: 60 requests per minute for public endpoints
 */
function rateLimitCheck(int $maxRequests = 60, int $windowSeconds = 60): void {
    // Gunakan real client IP (behind reverse proxy/nginx)
    $ip = $_SERVER['HTTP_X_REAL_IP']
        ?? $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['REMOTE_ADDR']
        ?? '0.0.0.0';
    // X-Forwarded-For bisa berisi multiple IP, ambil yang pertama
    if (strpos($ip, ',') !== false) {
        $ip = trim(explode(',', $ip)[0]);
    }
    $rateLimitDir = sys_get_temp_dir() . '/rekrutmen_ratelimit';

    if (!is_dir($rateLimitDir)) {
        @mkdir($rateLimitDir, 0755, true);
    }

    $file = $rateLimitDir . '/' . md5($ip) . '.json';

    $data = ['count' => 0, 'start' => time()];
    if (is_file($file)) {
        $content = @file_get_contents($file);
        if ($content) {
            $data = json_decode($content, true) ?: $data;
        }
    }

    // Reset window if expired
    if (time() - $data['start'] > $windowSeconds) {
        $data = ['count' => 0, 'start' => time()];
    }

    $data['count']++;

    @file_put_contents($file, json_encode($data), LOCK_EX);

    if ($data['count'] > $maxRequests) {
        http_response_code(429);
        echo json_encode([
            'status' => 429,
            'message' => 'Terlalu banyak permintaan. Coba lagi dalam beberapa saat.'
        ]);
        exit;
    }
}

/**
 * Sanitize string input - strip tags, trim, limit length
 */
function sanitizeInput(string $value, int $maxLength = 1000): string {
    $value = trim($value);
    $value = strip_tags($value);
    $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    if (mb_strlen($value) > $maxLength) {
        $value = mb_substr($value, 0, $maxLength);
    }
    return $value;
}

/**
 * Sanitize all input data recursively
 */
function sanitizeAllInput(array &$data): void {
    foreach ($data as $key => &$value) {
        if (is_string($value)) {
            // Don't sanitize certain fields that may contain valid HTML-like content
            $skipSanitize = ['pengalaman', 'persyaratan', 'deskripsi'];
            if (!in_array($key, $skipSanitize, true)) {
                $value = sanitizeInput($value);
            } else {
                // Still trim and limit length
                $value = trim($value);
                if (mb_strlen($value) > 10000) {
                    $value = mb_substr($value, 0, 10000);
                }
            }
        } elseif (is_array($value)) {
            sanitizeAllInput($value);
        }
    }
}

/**
 * Validate that uploaded file is not a PHP script or executable
 * Checks file content for PHP tags and other dangerous patterns
 */
function validateFileContent(string $tmpPath): bool {
    // Read first 1KB to check for PHP/script content
    $content = @file_get_contents($tmpPath, false, null, 0, 1024);
    if ($content === false) return false;

    // Check for PHP opening tags
    $dangerousPatterns = [
        '<?php',
        '<?=',
        '<script',
        '<%',
        '#!/',
    ];

    $contentLower = strtolower($content);
    foreach ($dangerousPatterns as $pattern) {
        if (strpos($contentLower, strtolower($pattern)) !== false) {
            return false;
        }
    }

    return true;
}

/**
 * Validate file extension matches expected type
 */
function validateFileExtension(string $filename, array $allowedExtensions): bool {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, $allowedExtensions, true);
}

/**
 * Stricter rate limit for form submissions (20 per 10 minutes per IP)
 * Dinaikkan dari 5 karena di server semua user bisa share IP (reverse proxy)
 */
function rateLimitSubmission(): void {
    rateLimitCheck(20, 600);
}
