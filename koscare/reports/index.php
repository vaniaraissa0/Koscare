<?php
/**
 * KosCare - Daftar Laporan Kerusakan
 * Otomatis terfilter untuk Penghuni (hanya miliknya) atau Pengelola (semua laporan)
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

// Parameter Filter
$statusFilter = trim($_GET['status'] ?? '');
$categoryFilter = trim($_GET['category'] ?? '');
$urgencyFilter = trim($_GET['urgency'] ?? '');
$searchKeyword = trim($_GET['q'] ?? '');

$sql = "
    SELECT r.*, u.name AS reporter_name, u.room_number, u.phone AS reporter_phone
    FROM reports r
    JOIN users u ON r.user_id = u.id
    WHERE 1=1
";
$params = [];

// Jika Penghuni, BATASI HANYA laporan miliknya sendiri
if (!$isManager) {
    $sql .= " AND r.user_id = :uid";
    $params['uid'] = $user['id'];
}

if (!empty($statusFilter)) {
    $sql .= " AND r.status = :status";
    $params['status'] = $statusFilter;
}

if (!empty($categoryFilter)) {
    $sql .= " AND r.category = :category";
    $params['category'] = $categoryFilter;
}

if (!empty($urgencyFilter)) {
    $sql .= " AND r.urgency = :urgency";
    $params['urgency'] = $urgencyFilter;
}

if (!empty($searchKeyword)) {
    $sql .= " AND (r.ticket_number LIKE :kw1 OR r.title LIKE :kw2 OR r.location LIKE :kw3)";
    $params['kw1'] = "%{$searchKeyword}%";
    $params['kw2'] = "%{$searchKeyword}%";
    $params['kw3'] = "%{$searchKeyword}%";
}

$sql .= " ORDER BY r.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reports = $stmt->fetchAll();

$pageTitle = ($isManager ? 'Semua Laporan Kerusakan' : 'Laporan Saya') . ' — ' . APP_NAME;
$activeNav = $isManager ? 'all_reports' : 'my_reports';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-4 my-2">
    <div class="row g-4">
        <!-- Sidebar Desktop -->
        <div class="col-lg-3">
            <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
        </div>

        <div class="col-lg-9">
            <?php renderFlash(); ?>

            <div class="card card-custom p-4 mb-4 shadow-xs">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 pb-3 mb-3 border-bottom">
                    <div>
                        <h4 class="fw-bold text-charcoal mb-1">
                            <?php echo $isManager ? 'Semua Laporan Kerusakan Kos' : 'Daftar Laporan Fasilitas Saya'; ?>
                        </h4>
                        <p class="text-taupe fs-7 mb-0">
                            <?php echo $isManager ? 'Kelola dan tindak lanjuti laporan seluruh penghuni kos' : 'Pantau progres penanganan kerusakan yang pernah Anda laporkan'; ?>
                        </p>
                    </div>
                    <?php if (!$isManager): ?>
                        <a href="<?php echo BASE_URL; ?>/reports/create.php" class="btn btn-sage px-3.5 py-2 rounded-pill text-white fw-bold shadow-xs">
                            <i class="bi bi-plus-circle-fill me-1"></i> Buat Laporan
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Form Filter & Pencarian -->
                <form action="<?php echo BASE_URL; ?>/reports/index.php" method="GET" class="row g-2 mb-4">
                    <div class="col-md-4">
                        <input type="text" name="q" value="<?php echo e($searchKeyword); ?>" class="form-control form-control-sm" placeholder="Cari tiket, judul, kamar...">
                    </div>
                    <div class="col-6 col-md-3">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">Semua Status</option>
                            <?php foreach (ALL_STATUSES as $st): ?>
                                <option value="<?php echo e($st); ?>" <?php echo ($statusFilter === $st) ? 'selected' : ''; ?>>
                                    <?php echo e($st); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <select name="category" class="form-select form-select-sm">
                            <option value="">Semua Kategori</option>
                            <?php foreach (ALL_CATEGORIES as $cat): ?>
                                <option value="<?php echo e($cat); ?>" <?php echo ($categoryFilter === $cat) ? 'selected' : ''; ?>>
                                    <?php echo e($cat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-sage btn-sm w-100 rounded-3 text-white fw-semibold">
                            Filter
                        </button>
                        <?php if (!empty($searchKeyword) || !empty($statusFilter) || !empty($categoryFilter)): ?>
                            <a href="<?php echo BASE_URL; ?>/reports/index.php" class="btn btn-light btn-sm rounded-3 border" title="Reset">
                                <i class="bi bi-x"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>

                <!-- Tabel Laporan -->
                <?php if (empty($reports)): ?>
                    <div class="text-center py-5">
                        <div class="stat-icon-wrapper mx-auto mb-3 bg-light text-taupe">
                            <i class="bi bi-folder2-open"></i>
                        </div>
                        <h6 class="fw-bold text-charcoal">Tidak Ada Laporan</h6>
                        <p class="text-taupe fs-7 mb-3">Tidak ditemukan data laporan kerusakan yang sesuai kriteria.</p>
                        <?php if (!$isManager): ?>
                            <a href="<?php echo BASE_URL; ?>/reports/create.php" class="btn btn-sage btn-sm px-3 rounded-pill text-white fw-medium">
                                Buat Laporan Baru
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle table-hover mb-0">
                            <thead>
                                <tr class="text-taupe fs-8 text-uppercase border-bottom">
                                    <th>No. Tiket</th>
                                    <?php if ($isManager): ?>
                                        <th>Pelapor</th>
                                    <?php endif; ?>
                                    <th>Judul & Kategori</th>
                                    <th>Lokasi</th>
                                    <th>Urgensi</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th class="text-end">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reports as $rep): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-charcoal fs-7"><?php echo e($rep['ticket_number']); ?></span>
                                            <?php if (!empty($rep['photo'])): ?>
                                                <i class="bi bi-image text-sage ms-1" title="Foto lampiran tersedia"></i>
                                            <?php endif; ?>
                                        </td>
                                        <?php if ($isManager): ?>
                                            <td>
                                                <div class="fw-semibold text-charcoal fs-7"><?php echo e($rep['reporter_name']); ?></div>
                                                <small class="text-taupe fs-8"><?php echo e($rep['room_number']); ?></small>
                                            </td>
                                        <?php endif; ?>
                                        <td>
                                            <div class="fw-semibold text-charcoal fs-7"><?php echo e($rep['title']); ?></div>
                                            <small class="text-taupe fs-8"><?php echo e($rep['category']); ?></small>
                                        </td>
                                        <td>
                                            <span class="fs-7 text-charcoal"><?php echo e($rep['location']); ?></span>
                                        </td>
                                        <td>
                                            <?php echo renderUrgencyBadge($rep['urgency']); ?>
                                        </td>
                                        <td>
                                            <?php echo renderStatusBadge($rep['status']); ?>
                                        </td>
                                        <td>
                                            <span class="fs-8 text-taupe"><?php echo formatDateShort($rep['created_at']); ?></span>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?php echo BASE_URL; ?>/reports/detail.php?id=<?php echo (int)$rep['id']; ?>" class="btn btn-sm btn-outline-sage rounded-pill px-3 py-1">
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
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
