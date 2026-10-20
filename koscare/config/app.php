<?php
/**
 * KosCare - Konfigurasi Aplikasi & Sesi
 */

defined('KOSCARE_APP') or define('KOSCARE_APP', true);

// Set zona waktu Indonesia
date_default_timezone_set('Asia/Jakarta');

// Konfigurasi URL dan Path Dasar
define('APP_NAME', 'KosCare');
define('APP_TAGLINE', 'Perawatan Kos Jadi Lebih Mudah');
define('BASE_URL', '/koscare');

// Path Folder Unggahan
define('UPLOAD_DIR', __DIR__ . '/../uploads/reports/');
define('UPLOAD_URL', BASE_URL . '/uploads/reports/');

// Batasan Unggahan
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2 MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);
define('ALLOWED_MIME_TYPES', ['image/jpeg', 'image/png', 'image/webp']);

// Status Laporan Resmi
define('STATUS_WAITING', 'Menunggu Pemeriksaan');
define('STATUS_PROCESSING', 'Sedang Diproses');
define('STATUS_NEED_INFO', 'Menunggu Informasi');
define('STATUS_COMPLETED', 'Selesai');
define('STATUS_REJECTED', 'Ditolak');

const ALL_STATUSES = [
    STATUS_WAITING,
    STATUS_PROCESSING,
    STATUS_NEED_INFO,
    STATUS_COMPLETED,
    STATUS_REJECTED
];

// Tingkat Urgensi
const ALL_URGENCIES = [
    'Rendah',
    'Sedang',
    'Tinggi',
    'Darurat'
];

// Kategori Kerusakan
const ALL_CATEGORIES = [
    'Listrik',
    'Air dan sanitasi',
    'Furnitur',
    'Bangunan',
    'Kebersihan',
    'Lainnya'
];

// Inisialisasi Sesi Aman jika belum berjalan
if (session_status() === PHP_SESSION_NONE) {
    // Amankan parameter cookie sesi
    $cookieParams = [
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax'
    ];
    session_set_cookie_params($cookieParams);
    session_start();
}
