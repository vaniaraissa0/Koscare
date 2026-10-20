<?php
/**
 * KosCare - Sesi Keluar (Logout)
 */

define('KOSCARE_APP', true);

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

logoutUser();

// Start fresh session solely to hold the flash message
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
setFlash('info', 'Anda telah berhasil keluar dari akun KosCare.');

header('Location: ' . BASE_URL . '/login.php');
exit;
