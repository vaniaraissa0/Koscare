<?php
/**
 * KosCare - Dashboard Pengelola
 * Desain Pastel Lilac Aesthetic
 */

define('KOSCARE_APP', true);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requirePengelola();

$user = currentUser();
$pdo = getDBConnection();

$stmtStats = $pdo->query("
    SELECT 
        COUNT(*) AS total_all,
        SUM(CASE WHEN status = 'Menunggu Pemeriksaan' THEN 1 ELSE 0 END) AS waiting_count,
        SUM(CASE WHEN status = 'Sedang Diproses' THEN 1 ELSE 0 END) AS processing_count,
        SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END) AS completed_count
    FROM reports
");
$stats = $stmtStats->fetch() ?: [
    'total_all'        => 0,
    'waiting_count'    => 0,
    'processing_count' => 0,
    'completed_count'  => 0
];

$statusFilter = trim($_GET['status'] ?? '');
$categoryFilter = trim($_GET['category'] ?? '');
$searchKeyword = trim($_GET['q'] ?? '');

$sql = "
    SELECT r.*, u.name AS reporter_name, u.room_number, u.phone AS reporter_phone
    FROM reports r
    JOIN users u ON r.user_id = u.id
    WHERE 1=1
";
$params = [];

if (!empty($statusFilter)) {
    $sql .= " AND r.status = :status";
    $params['status'] = $statusFilter;
}

if (!empty($categoryFilter)) {
    $sql .= " AND r.category = :category";
    $params['category'] = $categoryFilter;
}

if (!empty($searchKeyword)) {
    $sql .= " AND (r.title LIKE :kw1 OR r.location LIKE :kw2 OR u.name LIKE :kw3)";
    $params['kw1'] = "%{$searchKeyword}%";
    $params['kw2'] = "%{$searchKeyword}%";
    $params['kw3'] = "%{$searchKeyword}%";
}

$sql .= " ORDER BY r.created_at DESC";

$stmtList = $pdo->prepare($sql);
$stmtList->execute($params);
$reports = $stmtList->fetchAll();

$pageTitle = 'Dashboard Pengelola — ' . APP_NAME;
$activeNav = 'dashboard';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-4 my-2">
    <?php renderFlash(); ?>

    <!-- Header Pengelola -->
    <div class="card card-aesthetic p-4 p-md-5 mb-4 shadow-sm" style="background-color: #FFFFFF;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge-pastel mb-2" style="background-color: var(--color-light-lilac); color: var(--color-primary-purple);">
                    Pengelola Kos
                </span>
                <h2 class="fw-bold text-charcoal mb-1">
                    Panel Pengelola — <?php echo e($user['name']); ?>
                </h2>
                <p class="text-muted small mb-0">
                    Pemeriksaan laporan, pembaruan status perbaikan, dan tanggapan kepada penghuni kos.
                </p>
            </div>
            <div>
                <a href="<?php echo BASE_URL; ?>/repairs/history.php" class="btn btn-outline-purple px-4 py-2">
                    <i class="bi bi-clock-history"></i>
                    <span>Riwayat Perbaikan</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Ringkasan Statistik 4 Kolom -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card card-aesthetic p-4 text-center">
                <span class="text-muted small text-uppercase fw-bold">Semua Laporan</span>
                <h3 class="fw-bold text-charcoal my-1"><?php echo (int)$stats['total_all']; ?></h3>
                <span class="text-muted small">Total masuk</span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card card-aesthetic p-4 text-center">
                <span class="small text-uppercase fw-bold" style="color: #856404;">Menunggu</span>
                <h3 class="fw-bold my-1" style="color: #856404;"><?php echo (int)$stats['waiting_count']; ?></h3>
                <span class="text-muted small">Perlu diperiksa</span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card card-aesthetic p-4 text-center">
                <span class="small text-uppercase fw-bold" style="color: #553E8A;">Sedang Diproses</span>
                <h3 class="fw-bold my-1" style="color: var(--color-primary-purple);"><?php echo (int)$stats['processing_count']; ?></h3>
                <span class="text-muted small">Dalam pengerjaan</span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card card-aesthetic p-4 text-center">
                <span class="text-success small text-uppercase fw-bold">Selesai</span>
                <h3 class="fw-bold text-success my-1"><?php echo (int)$stats['completed_count']; ?></h3>
                <span class="text-muted small">Tuntas</span>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="card card-aesthetic p-3 p-md-4 mb-4 shadow-sm">
        <form action="<?php echo BASE_URL; ?>/dashboard/pengelola.php" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="q" value="<?php echo e($searchKeyword); ?>" class="form-control" placeholder="Cari judul, lokasi kamar, atau pelapor...">
            </div>
            <div class="col-6 col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <?php foreach (ALL_STATUSES as $st): ?>
                        <option value="<?php echo e($st); ?>" <?php echo ($statusFilter === $st) ? 'selected' : ''; ?>>
                            <?php echo e($st); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select name="category" class="form-select">
                    <option value="">Semua Kategori</option>
                    <?php foreach (ALL_CATEGORIES as $cat): ?>
                        <option value="<?php echo e($cat); ?>" <?php echo ($categoryFilter === $cat) ? 'selected' : ''; ?>>
                            <?php echo e($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary-purple w-100 py-2">
                    Cari
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Seluruh Laporan -->
    <div class="card card-aesthetic p-4 p-md-5 shadow-sm">
        <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
            <h4 class="fw-bold text-charcoal mb-0">Daftar Laporan Fasilitas</h4>
            <span class="text-muted small"><?php echo count($reports); ?> Laporan</span>
        </div>

        <?php if (empty($reports)): ?>
            <div class="text-center py-5 text-muted small">
                Tidak ada laporan yang sesuai dengan kriteria pencarian.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr class="text-muted small border-bottom">
                            <th>Pelapor</th>
                            <th>Judul & Kategori</th>
                            <th>Lokasi</th>
                            <th>Urgensi</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th class="text-end">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $rep): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-charcoal"><?php echo e($rep['reporter_name']); ?></div>
                                    <span class="text-muted small"><?php echo e($rep['room_number'] ?? '-'); ?></span>
                                </td>
                                <td>
                                    <div class="fw-bold text-charcoal"><?php echo e($rep['title']); ?></div>
                                    <span class="text-muted small"><?php echo e($rep['category']); ?></span>
                                </td>
                                <td class="small text-charcoal"><?php echo e($rep['location']); ?></td>
                                <td><?php echo renderUrgencyBadge($rep['urgency']); ?></td>
                                <td><?php echo renderStatusBadge($rep['status']); ?></td>
                                <td class="text-muted small"><?php echo formatDateShort($rep['created_at']); ?></td>
                                <td class="text-end">
                                    <a href="<?php echo BASE_URL; ?>/reports/detail.php?id=<?php echo (int)$rep['id']; ?>" class="btn btn-primary-purple btn-sm px-3 py-1">
                                        Kelola
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
