/**
 * Manifest dan konten lengkap seluruh file proyek PHP Native KosCare untuk XAMPP
 * Digunakan untuk Code Inspector dan One-Click ZIP Downloader
 */

export interface ProjectFile {
  path: string;
  category: 'database' | 'config' | 'includes' | 'pages' | 'assets' | 'docs' | 'setup';
  description: string;
  language: string;
  content: string;
}

export const PHP_PROJECT_FILES: ProjectFile[] = [
  {
    path: 'database/koscare.sql',
    category: 'database',
    description: 'Skema tabel MySQL koscare_db, relasi foreign key, index, dan data awal demo',
    language: 'sql',
    content: `-- ==============================================================
-- DATABASE: koscare_db
-- SISTEM PELAPORAN DAN PEMANTAUAN KERUSAKAN FASILITAS KOS (KosCare)
-- Kompatibel dengan MySQL 5.7+ / MariaDB 10.4+ (XAMPP Windows)
-- ==============================================================

CREATE DATABASE IF NOT EXISTS \`koscare_db\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE \`koscare_db\`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS \`users\`;
CREATE TABLE \`users\` (
  \`id\` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  \`name\` VARCHAR(100) NOT NULL,
  \`username\` VARCHAR(50) NOT NULL,
  \`email\` VARCHAR(100) NOT NULL,
  \`password\` VARCHAR(255) NOT NULL,
  \`phone\` VARCHAR(20) NOT NULL,
  \`room_number\` VARCHAR(20) DEFAULT NULL,
  \`role\` ENUM('penghuni', 'pengelola', 'teknisi') NOT NULL DEFAULT 'penghuni',
  \`status\` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  \`created_at\` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  \`updated_at\` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (\`id\`),
  UNIQUE KEY \`uk_users_username\` (\`username\`),
  UNIQUE KEY \`uk_users_email\` (\`email\`),
  KEY \`idx_users_role\` (\`role\`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`reports\`;
CREATE TABLE \`reports\` (
  \`id\` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  \`ticket_number\` VARCHAR(30) NOT NULL,
  \`user_id\` INT UNSIGNED NOT NULL,
  \`title\` VARCHAR(150) NOT NULL,
  \`category\` ENUM(
    'Listrik dan lampu',
    'Air dan pipa',
    'Kamar mandi',
    'Pintu dan kunci',
    'Furnitur',
    'Internet/Wi-Fi',
    'Fasilitas umum',
    'Lainnya'
  ) NOT NULL,
  \`location\` VARCHAR(100) NOT NULL,
  \`description\` TEXT NOT NULL,
  \`urgency\` ENUM('Rendah', 'Sedang', 'Tinggi', 'Darurat') NOT NULL DEFAULT 'Sedang',
  \`photo\` VARCHAR(255) DEFAULT NULL,
  \`status\` ENUM(
    'Menunggu Pemeriksaan',
    'Sedang Diproses',
    'Menunggu Informasi',
    'Selesai',
    'Ditolak'
  ) NOT NULL DEFAULT 'Menunggu Pemeriksaan',
  \`created_at\` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  \`updated_at\` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (\`id\`),
  UNIQUE KEY \`uk_reports_ticket\` (\`ticket_number\`),
  KEY \`idx_reports_user_id\` (\`user_id\`),
  KEY \`idx_reports_status\` (\`status\`),
  KEY \`idx_reports_urgency\` (\`urgency\`),
  CONSTRAINT \`fk_reports_user\` FOREIGN KEY (\`user_id\`) REFERENCES \`users\` (\`id\`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`report_status_history\`;
CREATE TABLE \`report_status_history\` (
  \`id\` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  \`report_id\` INT UNSIGNED NOT NULL,
  \`changed_by_user_id\` INT UNSIGNED NOT NULL,
  \`previous_status\` VARCHAR(50) NOT NULL,
  \`new_status\` VARCHAR(50) NOT NULL,
  \`notes\` TEXT DEFAULT NULL,
  \`created_at\` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (\`id\`),
  KEY \`idx_history_report_id\` (\`report_id\`),
  KEY \`idx_history_user_id\` (\`changed_by_user_id\`),
  CONSTRAINT \`fk_history_report\` FOREIGN KEY (\`report_id\`) REFERENCES \`reports\` (\`id\`) ON DELETE CASCADE,
  CONSTRAINT \`fk_history_user\` FOREIGN KEY (\`changed_by_user_id\`) REFERENCES \`users\` (\`id\`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`report_comments\`;
CREATE TABLE \`report_comments\` (
  \`id\` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  \`report_id\` INT UNSIGNED NOT NULL,
  \`user_id\` INT UNSIGNED NOT NULL,
  \`comment\` TEXT NOT NULL,
  \`created_at\` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (\`id\`),
  KEY \`idx_comments_report_id\` (\`report_id\`),
  KEY \`idx_comments_user_id\` (\`user_id\`),
  CONSTRAINT \`fk_comments_report\` FOREIGN KEY (\`report_id\`) REFERENCES \`reports\` (\`id\`) ON DELETE CASCADE,
  CONSTRAINT \`fk_comments_user\` FOREIGN KEY (\`user_id\`) REFERENCES \`users\` (\`id\`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`repair_records\`;
CREATE TABLE \`repair_records\` (
  \`id\` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  \`report_id\` INT UNSIGNED NOT NULL,
  \`technician_name\` VARCHAR(100) DEFAULT 'Tim Teknisi KosCare',
  \`completion_date\` DATE NOT NULL,
  \`repair_notes\` TEXT NOT NULL,
  \`cost_estimate\` DECIMAL(10,2) DEFAULT 0.00,
  \`created_at\` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (\`id\`),
  UNIQUE KEY \`uk_repair_report\` (\`report_id\`),
  CONSTRAINT \`fk_repair_report\` FOREIGN KEY (\`report_id\`) REFERENCES \`reports\` (\`id\`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`technician_assignments\`;
CREATE TABLE \`technician_assignments\` (
  \`id\` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  \`report_id\` INT UNSIGNED NOT NULL,
  \`technician_user_id\` INT UNSIGNED NOT NULL,
  \`assigned_by_user_id\` INT UNSIGNED NOT NULL,
  \`assigned_at\` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  \`status\` ENUM('assigned', 'in_progress', 'completed') NOT NULL DEFAULT 'assigned',
  \`notes\` TEXT DEFAULT NULL,
  PRIMARY KEY (\`id\`),
  KEY \`idx_assign_report\` (\`report_id\`),
  KEY \`idx_assign_tech\` (\`technician_user_id\`),
  CONSTRAINT \`fk_assign_report\` FOREIGN KEY (\`report_id\`) REFERENCES \`reports\` (\`id\`) ON DELETE CASCADE,
  CONSTRAINT \`fk_assign_tech\` FOREIGN KEY (\`technician_user_id\`) REFERENCES \`users\` (\`id\`) ON DELETE CASCADE,
  CONSTRAINT \`fk_assign_by\` FOREIGN KEY (\`assigned_by_user_id\`) REFERENCES \`users\` (\`id\`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS \`activity_logs\`;
CREATE TABLE \`activity_logs\` (
  \`id\` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  \`user_id\` INT UNSIGNED DEFAULT NULL,
  \`action\` VARCHAR(100) NOT NULL,
  \`details\` TEXT DEFAULT NULL,
  \`ip_address\` VARCHAR(45) DEFAULT NULL,
  \`created_at\` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (\`id\`),
  KEY \`idx_logs_user_id\` (\`user_id\`),
  KEY \`idx_logs_created_at\` (\`created_at\`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- SEED DATA AWAL UNTUK PENGUJIAN
INSERT INTO \`users\` (\`id\`, \`name\`, \`username\`, \`email\`, \`password\`, \`phone\`, \`room_number\`, \`role\`, \`status\`) VALUES
(1, 'Vania Raissa', 'vania.raissa', 'vania.raissa@koscare.local', '$2y$10$sC4v904.xP5L1vJqP64JbOnb9Z4W8l6wXQO4l60u9Y6tJ9u8pP37y', '081234567890', 'Kantor Pengelola', 'pengelola', 'active'),
(2, 'Dimas Anggoro', 'dimas.anggoro', 'dimas@example.com', '$2y$10$5p8z9n6tY7m0K1vJqP64JbOnb9Z4W8l6wXQO4l60u9Y6tJ9u8pP37y', '081298765432', 'Kamar 102', 'penghuni', 'active'),
(3, 'Siti Nurhaliza', 'siti.nur', 'siti@example.com', '$2y$10$5p8z9n6tY7m0K1vJqP64JbOnb9Z4W8l6wXQO4l60u9Y6tJ9u8pP37y', '081345678912', 'Kamar 205', 'penghuni', 'active'),
(4, 'Pak Joko Santoso', 'joko.teknisi', 'joko@koscare.local', '$2y$10$5p8z9n6tY7m0K1vJqP64JbOnb9Z4W8l6wXQO4l60u9Y6tJ9u8pP37y', '081567890123', 'Workshop', 'teknisi', 'active');

INSERT INTO \`reports\` (\`id\`, \`ticket_number\`, \`user_id\`, \`title\`, \`category\`, \`location\`, \`description\`, \`urgency\`, \`photo\`, \`status\`, \`created_at\`) VALUES
(1, 'KC-202610-0001', 2, 'Kran Air Kamar Mandi Bocor Terus', 'Air dan pipa', 'Kamar 102', 'Kran wastafel tidak bisa ditutup rapat, air menetes deras sejak kemarin malam.', 'Tinggi', NULL, 'Sedang Diproses', '2026-10-18 09:30:00'),
(2, 'KC-202610-0002', 2, 'Lampu Kamar Mandi Mati Total', 'Listrik dan lampu', 'Kamar 102', 'Lampu downlight kamar mandi mendadak padam dan timbul bunyi mendengung saat saklar dinyalakan.', 'Sedang', NULL, 'Menunggu Pemeriksaan', '2026-10-19 14:15:00'),
(3, 'KC-202610-0003', 3, 'Gagang Pintu Kamar Longgar & Macet', 'Pintu dan kunci', 'Kamar 205', 'Kunci silinder sulit diputar dari dalam, khawatir terkunci dan tidak bisa keluar.', 'Darurat', NULL, 'Selesai', '2026-10-17 11:00:00'),
(4, 'KC-202610-0004', 3, 'Wi-Fi Lantai 2 Sering Putus', 'Internet/Wi-Fi', 'Lantai 2 Koridor', 'Sinyal router sering hilang mendadak setiap jam 7 malam.', 'Rendah', NULL, 'Menunggu Informasi', '2026-10-19 19:45:00');

INSERT INTO \`report_status_history\` (\`report_id\`, \`changed_by_user_id\`, \`previous_status\`, \`new_status\`, \`notes\`, \`created_at\`) VALUES
(1, 1, 'Menunggu Pemeriksaan', 'Sedang Diproses', 'Laporan diverifikasi. Ditugaskan ke Pak Joko untuk penggantian seal kran.', '2026-10-18 10:00:00'),
(3, 1, 'Menunggu Pemeriksaan', 'Sedang Diproses', 'Urgensi darurat diterima, langsung diprioritaskan teknisi kunci.', '2026-10-17 11:30:00'),
(3, 1, 'Sedang Diproses', 'Selesai', 'Silinder kunci pintu kamar 205 telah diganti dengan unit baru Dekkson.', '2026-10-17 14:00:00'),
(4, 1, 'Menunggu Pemeriksaan', 'Menunggu Informasi', 'Mohon konfirmasi lampu indikator WAN pada modem berwarna apa?', '2026-10-20 08:30:00');

INSERT INTO \`repair_records\` (\`report_id\`, \`technician_name\`, \`completion_date\`, \`repair_notes\`, \`cost_estimate\`, \`created_at\`) VALUES
(3, 'Pak Joko Santoso', '2026-10-17', 'Penggantian 1 set lockcase dan silinder pintu Kamar 205. Diserahkan 3 anak kunci baru.', 125000.00, '2026-10-17 14:05:00');
`
  },
  {
    path: 'config/database.php',
    category: 'config',
    description: 'Koneksi database PDO dengan pencegahan SQL Injection dan penanganan error ramah',
    language: 'php',
    content: `<?php
/**
 * KosCare - Konfigurasi Koneksi Database MySQL via PDO
 * Kompatibel dengan XAMPP Windows & PHP 8.3
 */

defined('KOSCARE_APP') or define('KOSCARE_APP', true);

define('DB_HOST', 'localhost');
define('DB_NAME', 'koscare_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PORT', '3306');
define('DB_CHARSET', 'utf8mb4');

function getDBConnection(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', DB_HOST, DB_PORT, DB_NAME, DB_CHARSET);
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log('[KosCare DB Error] ' . $e->getMessage());
            http_response_code(500);
            echo "<h3>Gagal terhubung ke MySQL. Pastikan modul MySQL di XAMPP menyala dan database koscare_db telah diimpor.</h3>";
            exit;
        }
    }
    return $pdo;
}
`
  },
  {
    path: 'config/app.php',
    category: 'config',
    description: 'Konstanta sistem, daftar status laporan, kategori kerusakan, dan inisialisasi sesi aman',
    language: 'php',
    content: `<?php
/**
 * KosCare - Konfigurasi Aplikasi & Sesi
 */

defined('KOSCARE_APP') or define('KOSCARE_APP', true);

date_default_timezone_set('Asia/Jakarta');

define('APP_NAME', 'KosCare');
define('APP_TAGLINE', 'Hunian Nyaman, Perbaikan Terpantau');
define('BASE_URL', '/koscare');

define('UPLOAD_DIR', __DIR__ . '/../uploads/reports/');
define('UPLOAD_URL', BASE_URL . '/uploads/reports/');

define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2 MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);
define('ALLOWED_MIME_TYPES', ['image/jpeg', 'image/png', 'image/webp']);

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

const ALL_URGENCIES = ['Rendah', 'Sedang', 'Tinggi', 'Darurat'];

const ALL_CATEGORIES = [
    'Listrik dan lampu',
    'Air dan pipa',
    'Kamar mandi',
    'Pintu dan kunci',
    'Furnitur',
    'Internet/Wi-Fi',
    'Fasilitas umum',
    'Lainnya'
];

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}
`
  },
  {
    path: 'includes/csrf.php',
    category: 'includes',
    description: 'Fungsi proteksi CSRF token dengan hash_equals()',
    language: 'php',
    content: `<?php
/**
 * KosCare - Proteksi CSRF
 */

defined('KOSCARE_APP') or define('KOSCARE_APP', true);

function generateCSRFToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function getCSRFToken(): string {
    return generateCSRFToken();
}

function csrfField(): string {
    $token = htmlspecialchars(getCSRFToken(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

function validateCSRFToken(?string $token): bool {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function requireCSRF(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!validateCSRFToken($token)) {
            http_response_code(403);
            die("<h3>Permintaan Ditolak: Token CSRF tidak valid atau kedaluwarsa.</h3>");
        }
    }
}
`
  },
  {
    path: 'includes/functions.php',
    category: 'includes',
    description: 'Sanitasi XSS e(), tiket otomatis, format tanggal, badge pastel status & urgensi',
    language: 'php',
    content: `<?php
/**
 * KosCare - Helper Sanitasi & Tampilan
 */

defined('KOSCARE_APP') or define('KOSCARE_APP', true);

function e(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function renderFlash(): void {
    $flash = getFlash();
    if ($flash) {
        $type = htmlspecialchars($flash['type']);
        $msg = htmlspecialchars($flash['message']);
        echo "<div class='alert alert-{$type} alert-dismissible fade show rounded-4 mb-4' role='alert'>{$msg}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

function generateTicketNumber(PDO $pdo): string {
    $prefix = 'KC-' . date('Ym') . '-';
    do {
        $ticket = $prefix . strtoupper(bin2hex(random_bytes(2)));
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reports WHERE ticket_number = :t");
        $stmt->execute(['t' => $ticket]);
    } while ($stmt->fetchColumn() > 0);
    return $ticket;
}

function logActivity(?int $userId, string $action, ?string $details = null): void {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (:u, :a, :d, :ip)");
        $stmt->execute(['u' => $userId, 'a' => $action, 'd' => $details, 'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1']);
    } catch (Exception $e) {}
}

function formatDateIndo(?string $datetime): string {
    if (!$datetime) return '-';
    return date('d M Y, H:i', strtotime($datetime)) . ' WIB';
}
`
  },
  {
    path: 'includes/auth.php',
    category: 'includes',
    description: 'Manajemen login, session_regenerate_id, otorisasi role-based, dan logout aman',
    language: 'php',
    content: `<?php
/**
 * KosCare - Autentikasi dan Otorisasi Sesi
 */

defined('KOSCARE_APP') or define('KOSCARE_APP', true);

function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}

function currentUser(): ?array {
    if (!isLoggedIn()) return null;
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

function currentRole(): ?string {
    return $_SESSION['user_role'] ?? null;
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function requireRole($roles): void {
    requireLogin();
    $roles = (array)$roles;
    if (!in_array(currentRole(), $roles, true)) {
        http_response_code(403);
        die('Akses ditolak: Anda tidak memiliki izin untuk halaman ini.');
    }
}

function requirePenghuni(): void { requireRole(['penghuni']); }
function requirePengelola(): void { requireRole(['pengelola']); }
function requireTeknisi(): void { requireRole(['teknisi']); }

function loginUser(array $user): void {
    session_regenerate_id(true);
    $_SESSION['user_id']          = (int)$user['id'];
    $_SESSION['user_name']        = $user['name'];
    $_SESSION['user_username']    = $user['username'];
    $_SESSION['user_email']       = $user['email'];
    $_SESSION['user_role']        = $user['role'];
    $_SESSION['user_room_number'] = $user['room_number'] ?? '';
    $_SESSION['user_phone']       = $user['phone'] ?? '';
}

function logoutUser(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
    }
    session_destroy();
}
`
  },
  {
    path: 'setup/create_admin.php',
    category: 'setup',
    description: 'Skrip inisialisasi akun Vania Raissa dengan BCRYPT dan proteksi setup.lock',
    language: 'php',
    content: `<?php
/**
 * KosCare - Setup Pengelola Sekali Pakai (One-Time Setup)
 * Pengelola: Vania Raissa (vania.raissa / VaniaR@1)
 */
define('KOSCARE_APP', true);
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$lockFile = __DIR__ . '/setup.lock';
if (file_exists($lockFile)) {
    die("Setup telah dikunci untuk keamanan.");
}

$pdo = getDBConnection();
$hash = password_hash('VaniaR@1', PASSWORD_BCRYPT);

$stmt = $pdo->prepare("
    INSERT INTO users (name, username, email, password, phone, room_number, role, status)
    VALUES ('Vania Raissa', 'vania.raissa', 'vania.raissa@koscare.local', :hash, '081234567890', 'Kantor Pengelola', 'pengelola', 'active')
    ON DUPLICATE KEY UPDATE password = VALUES(password)
");
$stmt->execute(['hash' => $hash]);
file_put_contents($lockFile, "Locked at " . date('Y-m-d H:i:s'));
echo "Setup pengelola berhasil! Silakan login di http://localhost/koscare/login.php";
`
  },
  {
    path: 'uploads/reports/.htaccess',
    category: 'assets',
    description: 'Aturan Apache mencegah eksekusi skrip PHP di folder unggahan untuk keamanan maksimal',
    language: 'apache',
    content: `# Nonaktifkan eksekusi PHP pada folder uploads
<IfModule mod_php.c>
    php_flag engine off
</IfModule>
<IfModule mod_php7.c>
    php_flag engine off
</IfModule>
<IfModule mod_php8.c>
    php_flag engine off
</IfModule>

<FilesMatch "(?i)\\.(php|phtml|php3|php4|php5|php7|php8|phar|inc|sh|bat|cmd|exe|pl|cgi)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>

Options -ExecCGI
Options -Indexes
`
  },
  {
    path: 'README.md',
    category: 'docs',
    description: 'Panduan instalasi langkah demi langkah di XAMPP Windows dan solusi troubleshooting',
    language: 'markdown',
    content: `# Panduan Instalasi KosCare di XAMPP Windows

1. Salin folder \`koscare\` ke \`C:\\xampp\\htdocs\\koscare\`
2. Buka XAMPP Control Panel dan nyalakan Apache serta MySQL (berwarna hijau)
3. Buka \`http://localhost/phpmyadmin/\`
4. Buat database baru bernama \`koscare_db\` lalu impor file \`database/koscare.sql\`
5. Buka \`http://localhost/koscare/setup/create_admin.php\` untuk menginisialisasi akun pengelola
6. Buka \`http://localhost/koscare/\` untuk mulai menggunakan aplikasi!

Kredensial Pengelola:
- Username: \`vania.raissa\`
- Password: \`VaniaR@1\`
`
  }
];
