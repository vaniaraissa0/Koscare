<?php
/**
 * KosCare - Riwayat Perbaikan Fasilitas yang Selesai
 * Desain Pastel Lilac Aesthetic
 */

define('KOSCARE_APP', true);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$user = currentUser();
$isManager = ($user['role'] === 'pengelola');
$pdo = getDBConnection();

$keyword = trim($_GET['q'] ?? '');

$sql = "
    SELECT r.id, r.ticket_number, r.title, r.category, r.location, r.created_at AS report_date, r.status,
           u.name AS reporter_name, u.room_number,
           rr.technician_name, rr.completion_date, rr.repair_notes, rr.cost_estimate
    FROM reports r
    JOIN users u ON r.user_id = u.id
    JOIN repair_records rr ON r.id = rr.report_id
    WHERE r.status = 'Selesai'
";
$params = [];

if (!$isManager) {
    $sql .= " AND r.user_id = :uid";
    $params['uid'] = $user['id'];
}

if (!empty($keyword)) {
    $sql .= " AND (r.title LIKE :k1 OR r.location LIKE :k2 OR rr.technician_name LIKE :k3)";
    $params['k1'] = "%{$keyword}%";
    $params['k2'] = "%{$keyword}%";
    $params['k3'] = "%{$keyword}%";
}

$sql .= " ORDER BY rr.completion_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$repairs = $stmt->fetchAll();

$pageTitle = 'Riwayat Perbaikan Selesai — ' . APP_NAME;
$activeNav = 'repair_history';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-4 my-2">
    <?php renderFlash(); ?>

    <div class="card card-aesthetic p-4 p-md-5 mb-4 shadow-sm">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 pb-3 mb-3 border-bottom">
            <div>
                <h3 class="fw-bold text-charcoal mb-1">
                    Riwayat Perbaikan Selesai
                </h3>
                <p class="text-muted small mb-0">
                    Dokumentasi perbaikan fasilitas kos yang telah tuntas dikerjakan.
                </p>
            </div>
            <div>
                <a href="<?php echo BASE_URL . ($isManager ? '/dashboard/pengelola.php' : '/dashboard/penghuni.php'); ?>" class="btn btn-outline-purple btn-sm px-3">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>

        <form action="<?php echo BASE_URL; ?>/repairs/history.php" method="GET" class="row g-2 mb-4">
            <div class="col-md-6">
                <input type="text" name="q" value="<?php echo e($keyword); ?>" class="form-control" placeholder="Cari judul, lokasi, atau teknisi...">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary-purple px-4 py-2 small">
                    Cari
                </button>
            </div>
        </form>

        <?php if (empty($repairs)): ?>
            <div class="text-center py-5 text-muted small">
                Belum ada fasilitas yang tercatat selesai diperbaiki.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr class="text-muted small border-bottom">
                            <th>Kerusakan & Lokasi</th>
                            <th>Pelapor</th>
                            <th>Tgl Selesai</th>
                            <th>Teknisi & Catatan Hasil</th>
                            <th>Biaya</th>
                            <th class="text-end">Rincian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($repairs as $r): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-charcoal"><?php echo e($r['title']); ?></div>
                                    <span class="text-muted small"><?php echo e($r['location']); ?></span>
                                </td>
                                <td>
                                    <div class="fw-bold text-charcoal"><?php echo e($r['reporter_name']); ?></div>
                                    <span class="text-muted small"><?php echo e($r['room_number']); ?></span>
                                </td>
                                <td class="text-charcoal small">
                                    <?php echo formatDateShort($r['completion_date']); ?>
                                </td>
                                <td style="max-width: 250px;">
                                    <span class="fw-bold d-block small" style="color: #2B542C;"><?php echo e($r['technician_name'] ?: 'Tim Teknisi'); ?></span>
                                    <span class="text-muted small text-truncate d-block" title="<?php echo e($r['repair_notes']); ?>">
                                        <?php echo e($r['repair_notes']); ?>
                                    </span>
                                </td>
                                <td class="small font-semibold">
                                    <?php if ((float)$r['cost_estimate'] > 0): ?>
                                        Rp <?php echo number_format($r['cost_estimate'], 0, ',', '.'); ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?php echo BASE_URL; ?>/reports/detail.php?id=<?php echo (int)$r['id']; ?>" class="btn btn-outline-purple btn-sm px-3 py-1">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
