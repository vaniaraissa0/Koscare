<?php
/**
 * KosCare - Manajemen Autentikasi dan Otorisasi Sesi
 */

defined('KOSCARE_APP') or define('KOSCARE_APP', true);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

/**
 * Memeriksa apakah pengguna telah masuk (logged in)
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Mendapatkan data pengguna yang sedang login
 */
function currentUser(): ?array {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id'          => $_SESSION['user_id'],
        'name'        => $_SESSION['user_name'] ?? '',
        'username'    => $_SESSION['user_username'] ?? '',
        'email'       => $_SESSION['user_email'] ?? '',
        'role'        => $_SESSION['user_role'] ?? '',
        'room_number' => $_SESSION['user_room_number'] ?? '',
        'phone'       => $_SESSION['user_phone'] ?? ''
    ];
}

/**
 * Mendapatkan role pengguna saat ini
 */
function currentRole(): ?string {
    return $_SESSION['user_role'] ?? null;
}

/**
 * Mengharuskan pengguna untuk login terlebih dahulu
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        setFlash('warning', 'Silakan masuk terlebih dahulu untuk mengakses halaman tersebut.');
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

/**
 * Mengharuskan pengguna memiliki role tertentu
 */
function requireRole($allowedRoles): void {
    requireLogin();
    if (!is_array($allowedRoles)) {
        $allowedRoles = [$allowedRoles];
    }
    $userRole = currentRole();
    if (!in_array($userRole, $allowedRoles, true)) {
        http_response_code(403);
        setFlash('danger', 'Akses ditolak: Anda tidak memiliki izin untuk mengakses halaman ini.');
        
        // Alihkan ke dashboard masing-masing role
        if ($userRole === 'pengelola') {
            header('Location: ' . BASE_URL . '/dashboard/pengelola.php');
        } elseif ($userRole === 'teknisi') {
            header('Location: ' . BASE_URL . '/technician/dashboard.php');
        } else {
            header('Location: ' . BASE_URL . '/dashboard/penghuni.php');
        }
        exit;
    }
}

/**
 * Shortcut pembatasan akses khusus Penghuni
 */
function requirePenghuni(): void {
    requireRole(['penghuni']);
}

/**
 * Shortcut pembatasan akses khusus Pengelola
 */
function requirePengelola(): void {
    requireRole(['pengelola']);
}

/**
 * Shortcut pembatasan akses khusus Teknisi
 */
function requireTeknisi(): void {
    requireRole(['teknisi']);
}

/**
 * Inisialisasi sesi pengguna saat berhasil login
 * Menggunakan session_regenerate_id() untuk mencegah session fixation
 */
function loginUser(array $user): void {
    session_regenerate_id(true);
    $_SESSION['user_id']          = (int)$user['id'];
    $_SESSION['user_name']        = $user['name'];
    $_SESSION['user_username']    = $user['username'];
    $_SESSION['user_email']       = $user['email'];
    $_SESSION['user_role']        = $user['role'];
    $_SESSION['user_room_number'] = $user['room_number'] ?? '';
    $_SESSION['user_phone']       = $user['phone'] ?? '';
    $_SESSION['logged_in_at']     = time();

    // Catat log login
    logActivity((int)$user['id'], 'LOGIN', "Pengguna {$user['name']} ({$user['role']}) berhasil login");
}

/**
 * Menghapus sesi dan logout pengguna secara aman
 */
function logoutUser(): void {
    $user = currentUser();
    if ($user) {
        logActivity($user['id'], 'LOGOUT', "Pengguna {$user['name']} berhasil logout");
    }

    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
}
