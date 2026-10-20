<?php
/**
 * KosCare - Konfigurasi Koneksi Database MySQL via PDO
 * Kompatibel dengan XAMPP Windows & PHP 8.3
 */

// Cegah akses langsung jika diperlukan
defined('KOSCARE_APP') or define('KOSCARE_APP', true);

// Konfigurasi Database XAMPP Standar
define('DB_HOST', 'localhost');
define('DB_NAME', 'koscare_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PORT', '3306');
define('DB_CHARSET', 'utf8mb4');

/**
 * Mendapatkan instance koneksi PDO
 * @return PDO
 */
function getDBConnection(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Catat log internal, jangan bocorkan password ke layar browser
            error_log('[KosCare DB Error] ' . $e->getMessage());

            // Tampilkan pesan ramah pengguna jika database belum diimpor atau MySQL mati
            http_response_code(500);
            ?>
            <!DOCTYPE html>
            <html lang="id">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Koneksi Database Gagal - KosCare</title>
                <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
                <style>
                    body {
                        font-family: 'Plus Jakarta Sans', sans-serif;
                        background-color: #FFF9F4;
                        color: #3D3D46;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        min-height: 100vh;
                        margin: 0;
                        padding: 20px;
                    }
                    .error-card {
                        background: #ffffff;
                        padding: 32px;
                        border-radius: 16px;
                        max-width: 540px;
                        width: 100%;
                        box-shadow: 0 10px 30px rgba(0,0,0,0.06);
                        border: 1px solid #E8C6C6;
                    }
                    h2 { color: #d9534f; margin-top: 0; }
                    ol { padding-left: 20px; line-height: 1.6; color: #827A78; }
                    li { margin-bottom: 8px; }
                    .code-badge {
                        background: #FFF9F4;
                        padding: 2px 6px;
                        border-radius: 4px;
                        font-family: monospace;
                        color: #3D3D46;
                    }
                </style>
            </head>
            <body>
                <div class="error-card">
                    <h2>⚠️ Gagal Terhubung ke Database MySQL</h2>
                    <p>Aplikasi KosCare tidak dapat terhubung ke server MySQL XAMPP.</p>
                    <p><strong>Panduan Solusi Cepat:</strong></p>
                    <ol>
                        <li>Buka <strong>XAMPP Control Panel</strong> dan pastikan modul <strong>MySQL</strong> dalam status <em>Running</em> (berwarna hijau).</li>
                        <li>Buka <span class="code-badge">http://localhost/phpmyadmin/</span> di browser Anda.</li>
                        <li>Buat database bernama <span class="code-badge">koscare_db</span> lalu impor file <span class="code-badge">database/koscare.sql</span>.</li>
                        <li>Pastikan username database di <span class="code-badge">config/database.php</span> adalah <em>root</em> tanpa password (standar XAMPP).</li>
                    </ol>
                    <p style="margin-top: 24px; font-size: 13px; color: #827A78;">Pesan Sistem: <?php echo htmlspecialchars($e->getMessage()); ?></p>
                </div>
            </body>
            </html>
            <?php
            exit;
        }
    }

    return $pdo;
}

/**
 * Alias helper ringkas untuk getDBConnection()
 * @return PDO
 */
function getDB(): PDO {
    return getDBConnection();
}

