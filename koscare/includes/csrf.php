<?php
/**
 * KosCare - Proteksi CSRF (Cross-Site Request Forgery)
 */

defined('KOSCARE_APP') or define('KOSCARE_APP', true);

/**
 * Menghasilkan CSRF token unik dan menyimpannya ke dalam sesi
 */
function generateCSRFToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Mendapatkan CSRF token saat ini
 */
function getCSRFToken(): string {
    return generateCSRFToken();
}

/**
 * Merender hidden input form CSRF
 */
function csrfField(): string {
    $token = htmlspecialchars(getCSRFToken(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Memvalidasi token CSRF dari request POST
 */
function validateCSRFToken(?string $token): bool {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Memeriksa validasi CSRF untuk request metode POST
 */
function requireCSRF(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!validateCSRFToken($token)) {
            http_response_code(403);
            die("<h3>Permintaan Ditolak: Token CSRF tidak valid atau telah kedaluwarsa. Silakan refresh halaman dan coba lagi.</h3>");
        }
    }
}
