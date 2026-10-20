<?php
/**
 * KosCare - Kumpulan Fungsi Pembantu (Helpers & Sanitasi)
 */

defined('KOSCARE_APP') or define('KOSCARE_APP', true);

/**
 * Sanitasi output untuk mencegah serangan XSS
 */
function e(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Set flash message ke sesi pengguna
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

/**
 * Ambil dan hapus flash message dari sesi
 */
function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Tampilkan HTML alert flash message jika tersedia
 */
function renderFlash(): void {
    $flash = getFlash();
    if ($flash) {
        $type = htmlspecialchars($flash['type']);
        $msg = htmlspecialchars($flash['message']);
        
        $icon = 'bi-info-circle-fill';
        $alertClass = 'alert-info';
        if ($type === 'success') {
            $icon = 'bi-check-circle-fill';
            $alertClass = 'alert-success';
        } elseif ($type === 'danger') {
            $icon = 'bi-exclamation-triangle-fill';
            $alertClass = 'alert-danger';
        } elseif ($type === 'warning') {
            $icon = 'bi-exclamation-circle-fill';
            $alertClass = 'alert-warning';
        }

        echo <<<HTML
        <div class="alert {$alertClass} alert-dismissible fade show d-flex align-items-center shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi {$icon} fs-5 me-2"></i>
            <div class="flex-grow-1">{$msg}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
HTML;
    }
}

/**
 * Menghasilkan nomor tiket laporan unik (format: KC-YYYYMM-XXXX)
 */
function generateTicketNumber(PDO $pdo): string {
    $prefix = 'KC-' . date('Ym') . '-';
    $attempts = 0;
    do {
        $random = strtoupper(bin2hex(random_bytes(2))); // 4 karakter heksadesimal
        $ticket = $prefix . $random;
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reports WHERE ticket_number = :ticket");
        $stmt->execute(['ticket' => $ticket]);
        $exists = $stmt->fetchColumn() > 0;
        $attempts++;
    } while ($exists && $attempts < 10);

    return $ticket;
}

/**
 * Catat aktivitas penting pengguna ke tabel activity_logs
 */
function logActivity(?int $userId, string $action, ?string $details = null): void {
    try {
        $pdo = getDBConnection();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (:uid, :act, :det, :ip)");
        $stmt->execute([
            'uid' => $userId,
            'act' => $action,
            'det' => $details,
            'ip' => $ip
        ]);
    } catch (Exception $e) {
        // Jangan hentikan eksekusi jika logging gagal
        error_log('[KosCare Activity Log Error] ' . $e->getMessage());
    }
}

/**
 * Format tanggal Indonesia lengkap dengan jam
 */
function formatDateIndo(?string $datetime): string {
    if (!$datetime) return '-';
    $timestamp = strtotime($datetime);
    if (!$timestamp) return '-';

    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    $dayName = $hari[date('w', $timestamp)];
    $d = date('j', $timestamp);
    $m = $bulan[(int)date('n', $timestamp)];
    $y = date('Y', $timestamp);
    $time = date('H:i', $timestamp);

    return "{$d} {$m} {$y}, {$time} WIB";
}

/**
 * Format tanggal pendek (D M Y)
 */
function formatDateShort(?string $datetime): string {
    if (!$datetime) return '-';
    $timestamp = strtotime($datetime);
    if (!$timestamp) return '-';

    $bulan = [
        1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
        'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'
    ];
    $d = date('j', $timestamp);
    $m = $bulan[(int)date('n', $timestamp)];
    $y = date('Y', $timestamp);

    return "{$d} {$m} {$y}";
}

/**
 * Render badge status laporan dengan tema pastel aesthetic
 */
function renderStatusBadge(string $status): string {
    $colors = [
        'Menunggu Pemeriksaan' => ['bg' => '#FFF3CD', 'text' => '#856404'],
        'Sedang Diproses'      => ['bg' => '#EEE7FF', 'text' => '#553E8A'],
        'Menunggu Informasi'   => ['bg' => '#DCEBFF', 'text' => '#2B517D'],
        'Selesai'              => ['bg' => '#E2F0D9', 'text' => '#2B542C'],
        'Ditolak'              => ['bg' => '#FCE8E6', 'text' => '#8A332A']
    ];

    $cfg = $colors[$status] ?? ['bg' => '#F5F0FF', 'text' => '#454052'];
    $sStatus = htmlspecialchars($status, ENT_QUOTES, 'UTF-8');

    return sprintf(
        '<span class="d-inline-flex align-items-center px-2.5 py-1 rounded-pill fw-semibold" style="background-color: %s; color: %s; font-size: 0.78rem;">
            %s
        </span>',
        $cfg['bg'],
        $cfg['text'],
        $sStatus
    );
}

/**
 * Render badge urgensi kerusakan dengan warna pastel
 */
function renderUrgencyBadge(string $urgency): string {
    $colors = [
        'Rendah'  => ['bg' => '#F5F0FF', 'text' => '#553E8A'],
        'Sedang'  => ['bg' => '#DCEBFF', 'text' => '#2B517D'],
        'Tinggi'  => ['bg' => '#F8DCE8', 'text' => '#7A2E49'],
        'Darurat' => ['bg' => '#FCE8E6', 'text' => '#8A332A']
    ];

    $cfg = $colors[$urgency] ?? ['bg' => '#F5F0FF', 'text' => '#454052'];
    $sUrgency = htmlspecialchars($urgency, ENT_QUOTES, 'UTF-8');

    return sprintf(
        '<span class="d-inline-flex align-items-center px-2.5 py-1 rounded-pill" style="background-color: %s; color: %s; font-size: 0.75rem;">
            %s
        </span>',
        $cfg['bg'],
        $cfg['text'],
        $sUrgency
    );
}
