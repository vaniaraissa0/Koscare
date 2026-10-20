<?php
/**
 * KosCare - Dashboard Penghuni
 * Desain Pastel Lilac Aesthetic
 */

define('KOSCARE_APP', true);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requirePenghuni();

$user = currentUser();
$userId = (int)$user['id'];
$pdo = getDBConnection();

$stmtStats = $pdo->prepare("
    SELECT 
        COUNT(*) AS total_reports,
        SUM(CASE WHEN status IN ('Menunggu Pemeriksaan', 'Sedang Diproses', 'Menunggu Informasi') THEN 1 ELSE 0 END) AS in_progress_count,
        SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END) AS completed_count
    FROM reports
    WHERE user_id = :uid
");
$stmtStats->execute(['uid' => $userId]);
$stats = $stmtStats->fetch() ?: [
    'total_reports'     => 0,
    'in_progress_count' => 0,
    'completed_count'   => 0
];

$stmtRecent = $pdo->prepare("
    SELECT id, ticket_number, title, category, location, urgency, status, created_at
    FROM reports
    WHERE user_id = :uid
    ORDER BY created_at DESC
");
$stmtRecent->execute(['uid' => $userId]);
$myReports = $stmtRecent->fetchAll();

$pageTitle = 'Dashboard Penghuni — ' . APP_NAME;
$activeNav = 'dashboard';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-4 my-2">
    <?php renderFlash(); ?>

    <!-- Sapaan Penghuni -->
    <div class="card card-aesthetic p-4 p-md-5 mb-4 shadow-sm" style="background-color: #FFFFFF;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge-pastel mb-2" style="background-color: var(--color-light-lilac); color: var(--color-primary-purple);">
                    Penghuni <?php echo !empty($user['room_number']) ? '• ' . e($user['room_number']) : ''; ?>
                </span>
                <h2 class="fw-bold text-charcoal mb-1">
                    Halo, <?php echo e($user['name']); ?>
                </h2>
                <p class="text-muted small mb-0">
                    Sistem pemantauan fasilitas kamar Anda.
                </p>
            </div>
            <div>
                <a href="<?php echo BASE_URL; ?>/reports/create.php" class="btn btn-primary-purple px-4 py-2.5">
                    <i class="bi bi-plus-circle"></i>
                    <span>Buat Laporan</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Ringkasan Statistik Minimalis -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-aesthetic p-4 text-center">
                <span class="text-muted small text-uppercase fw-bold">Total Laporan</span>
                <h3 class="fw-bold text-charcoal my-1"><?php echo (int)$stats['total_reports']; ?></h3>
                <span class="text-muted small">Semua laporan yang Anda ajukan</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-aesthetic p-4 text-center" style="background-color: #FFFFFF;">
                <span class="small text-uppercase fw-bold" style="color: #553E8A;">Sedang Diproses</span>
                <h3 class="fw-bold my-1" style="color: var(--color-primary-purple);"><?php echo (int)$stats['in_progress_count']; ?></h3>
                <span class="text-muted small">Dalam antrean atau pengerjaan</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-aesthetic p-4 text-center">
                <span class="text-success small text-uppercase fw-bold">Selesai</span>
                <h3 class="fw-bold text-success my-1"><?php echo (int)$stats['completed_count']; ?></h3>
                <span class="text-muted small">Fasilitas berhasil diperbaiki</span>
            </div>
        </div>
    </div>

    <!-- Daftar Laporan Penghuni -->
    <div class="card card-aesthetic p-4 p-md-5 shadow-sm">
        <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
            <h4 class="fw-bold text-charcoal mb-0">Daftar Laporan Kerusakan</h4>
            <span class="text-muted small"><?php echo count($myReports); ?> Laporan</span>
        </div>

        <?php if (empty($myReports)): ?>
            <div class="text-center py-5">
                <p class="text-muted small mb-3">Belum ada laporan kerusakan yang Anda ajukan.</p>
                <a href="<?php echo BASE_URL; ?>/reports/create.php" class="btn btn-primary-purple px-4 py-2 small">
                    Buat Laporan Sekarang
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr class="text-muted small border-bottom">
                            <th>Tanggal</th>
                            <th>Judul & Kategori</th>
                            <th>Lokasi</th>
                            <th>Urgensi</th>
                            <th>Status</th>
                            <th class="text-end">Rincian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($myReports as $rep): ?>
                            <tr>
                                <td class="text-muted small">
                                    <?php echo formatDateShort($rep['created_at']); ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-charcoal"><?php echo e($rep['title']); ?></div>
                                    <span class="text-muted small"><?php echo e($rep['category']); ?></span>
                                </td>
                                <td class="small text-charcoal">
                                    <?php echo e($rep['location']); ?>
                                </td>
                                <td>
                                    <?php echo renderUrgencyBadge($rep['urgency']); ?>
                                </td>
                                <td>
                                    <?php echo renderStatusBadge($rep['status']); ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?php echo BASE_URL; ?>/reports/detail.php?id=<?php echo (int)$rep['id']; ?>" class="btn btn-outline-purple btn-sm px-3 py-1">
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
