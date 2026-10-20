<?php
/**
 * KosCare - Endpoint Pembaruan Status Laporan (Khusus Pengelola)
 * Menerapkan Transaksi Database PDO dan Validasi Riwayat Sesuai Aturan Sistem
 */

define('KOSCARE_APP', true);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// Otorisasi: Wajib Pengelola!
requirePengelola();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/dashboard/pengelola.php');
    exit;
}

requireCSRF();

$user = currentUser();
$pdo = getDBConnection();

$reportId = (int)($_POST['report_id'] ?? 0);
$newStatus = trim($_POST['new_status'] ?? '');
$notes = trim($_POST['notes'] ?? '');
$completionDate = trim($_POST['completion_date'] ?? date('Y-m-d'));
$technicianName = trim($_POST['technician_name'] ?? 'Tim Teknisi KosCare');
$costEstimate = (float)($_POST['cost_estimate'] ?? 0);

if ($reportId <= 0 || !in_array($newStatus, ALL_STATUSES, true)) {
    setFlash('danger', 'Permintaan perubahan status tidak valid.');
    header('Location: ' . BASE_URL . '/dashboard/pengelola.php');
    exit;
}

// Validasi Aturan Status Khusus (Section 9)
if ($newStatus === STATUS_COMPLETED) {
    if (empty($notes)) {
        setFlash('danger', 'Status "Selesai" wajib disertai dengan catatan hasil perbaikan.');
        header("Location: " . BASE_URL . "/reports/detail.php?id={$reportId}");
        exit;
    }
    if (empty($completionDate)) {
        setFlash('danger', 'Status "Selesai" wajib menyertakan tanggal penyelesaian.');
        header("Location: " . BASE_URL . "/reports/detail.php?id={$reportId}");
        exit;
    }
} elseif ($newStatus === STATUS_REJECTED) {
    if (empty($notes)) {
        setFlash('danger', 'Status "Ditolak" wajib disertai dengan alasan penolakan.');
        header("Location: " . BASE_URL . "/reports/detail.php?id={$reportId}");
        exit;
    }
} elseif ($newStatus === STATUS_NEED_INFO) {
    if (empty($notes)) {
        setFlash('danger', 'Status "Menunggu Informasi" wajib disertai dengan keterangan informasi yang diperlukan dari penghuni.');
        header("Location: " . BASE_URL . "/reports/detail.php?id={$reportId}");
        exit;
    }
}

try {
    $pdo->beginTransaction();

    // Ambil status sebelumnya
    $stmtCur = $pdo->prepare("SELECT ticket_number, status FROM reports WHERE id = :id FOR UPDATE");
    $stmtCur->execute(['id' => $reportId]);
    $current = $stmtCur->fetch();

    if (!$current) {
        throw new Exception('Laporan tidak ditemukan.');
    }

    $previousStatus = $current['status'];

    // 1. Update status laporan
    $stmtUp = $pdo->prepare("UPDATE reports SET status = :status, updated_at = NOW() WHERE id = :id");
    $stmtUp->execute(['status' => $newStatus, 'id' => $reportId]);

    // 2. Simpan ke report_status_history
    $stmtHist = $pdo->prepare("
        INSERT INTO report_status_history (
            report_id, changed_by_user_id, previous_status, new_status, notes, created_at
        ) VALUES (
            :rid, :uid, :prev, :new, :notes, NOW()
        )
    ");
    $stmtHist->execute([
        'rid'   => $reportId,
        'uid'   => $user['id'],
        'prev'  => $previousStatus,
        'new'   => $newStatus,
        'notes' => $notes
    ]);

    // 3. Jika status Selesai, catat/perbarui di tabel repair_records
    if ($newStatus === STATUS_COMPLETED) {
        $stmtRepair = $pdo->prepare("
            INSERT INTO repair_records (report_id, technician_name, completion_date, repair_notes, cost_estimate, created_at)
            VALUES (:rid, :tech, :cdate, :rnotes, :cost, NOW())
            ON DUPLICATE KEY UPDATE 
                technician_name = VALUES(technician_name),
                completion_date = VALUES(completion_date),
                repair_notes    = VALUES(repair_notes),
                cost_estimate   = VALUES(cost_estimate)
        ");
        $stmtRepair->execute([
            'rid'    => $reportId,
            'tech'   => $technicianName,
            'cdate'  => $completionDate,
            'rnotes' => $notes,
            'cost'   => $costEstimate
        ]);
    }

    // 4. Catat aktivitas penting
    logActivity(
        $user['id'],
        'UPDATE_STATUS',
        "Pengelola mengubah status laporan #{$current['ticket_number']} dari '{$previousStatus}' menjadi '{$newStatus}'"
    );

    $pdo->commit();

    setFlash('success', "Status laporan berhasil diubah menjadi: <strong>{$newStatus}</strong>.");
    header("Location: " . BASE_URL . "/reports/detail.php?id={$reportId}");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    setFlash('danger', 'Gagal memperbarui status: ' . htmlspecialchars($e->getMessage()));
    header("Location: " . BASE_URL . "/reports/detail.php?id={$reportId}");
    exit;
}
