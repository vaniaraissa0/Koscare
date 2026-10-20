<?php
/**
 * KosCare - Endpoint Penghapusan Laporan (Khusus Pengelola)
 */

define('KOSCARE_APP', true);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requirePengelola();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/dashboard/pengelola.php');
    exit;
}

requireCSRF();

$reportId = (int)($_POST['id'] ?? 0);
$user = currentUser();
$pdo = getDBConnection();

if ($reportId <= 0) {
    setFlash('danger', 'ID Laporan tidak valid.');
    header('Location: ' . BASE_URL . '/dashboard/pengelola.php');
    exit;
}

try {
    // Ambil data laporan sebelum dihapus
    $stmt = $pdo->prepare("SELECT ticket_number, photo FROM reports WHERE id = :id");
    $stmt->execute(['id' => $reportId]);
    $report = $stmt->fetch();

    if ($report) {
        // Hapus file foto dari server jika ada
        if (!empty($report['photo'])) {
            $photoPath = UPLOAD_DIR . $report['photo'];
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
        }

        // Hapus dari database (akan cascade ke history, comments, repair_records karena ON DELETE CASCADE)
        $del = $pdo->prepare("DELETE FROM reports WHERE id = :id");
        $del->execute(['id' => $reportId]);

        logActivity($user['id'], 'DELETE_REPORT', "Pengelola menghapus laporan #{$report['ticket_number']}");

        setFlash('success', "Laporan tiket <strong>{$report['ticket_number']}</strong> berhasil dihapus.");
    } else {
        setFlash('warning', 'Laporan tidak ditemukan.');
    }
} catch (Exception $e) {
    setFlash('danger', 'Gagal menghapus laporan: ' . htmlspecialchars($e->getMessage()));
}

header('Location: ' . BASE_URL . '/dashboard/pengelola.php');
exit;
