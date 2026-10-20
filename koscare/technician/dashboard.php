<?php
/**
 * KosCare - Dashboard Tugas Teknisi Lapangan
 */

define('KOSCARE_APP', true);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// Otorisasi: Khusus Teknisi atau Pengelola
requireRole(['teknisi', 'pengelola']);

$user = currentUser();
$pdo = getDBConnection();

// Ambil laporan yang berstatus 'Sedang Diproses' atau ditugaskan
$stmt = $pdo->query("
    SELECT r.*, u.name AS reporter_name, u.phone AS reporter_phone, u.room_number
    FROM reports r
    JOIN users u ON r.user_id = u.id
    WHERE r.status IN ('Sedang Diproses', 'Menunggu Pemeriksaan')
    ORDER BY 
        CASE WHEN r.urgency = 'Darurat' THEN 1 WHEN r.urgency = 'Tinggi' THEN 2 ELSE 3 END,
        r.created_at ASC
");
$tasks = $stmt->fetchAll();

$pageTitle = 'Dashboard Teknisi — ' . APP_NAME;
$activeNav = 'dashboard';

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

            <div class="card card-custom p-4 mb-4 shadow-xs" style="background: linear-gradient(135deg, #ffffff 65%, var(--color-sage-light));">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-sage-light text-sage fw-bold px-2.5 py-1 rounded-pill">Peran Teknisi Lapangan</span>
                </div>
                <h3 class="fw-bold text-charcoal mb-1">Daftar Antrean Tugas Perbaikan Fasilitas</h3>
                <p class="text-taupe fs-7 mb-0">
                    Daftar fasilitas kos yang memerlukan perbaikan fisik di lapangan. Koordinasikan dengan pengelola setelah pekerjaan selesai.
                </p>
            </div>

            <!-- List Tugas -->
            <?php if (empty($tasks)): ?>
                <div class="card card-custom p-5 text-center shadow-xs">
                    <div class="stat-icon-wrapper mx-auto mb-2 bg-success-subtle text-success">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <h6 class="fw-bold text-charcoal">Tidak Ada Antrean Pekerjaan Aktif</h6>
                    <p class="text-taupe fs-7 mb-0">Semua laporan kerusakan saat ini telah selesai atau belum ada disposisi baru.</p>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($tasks as $t): ?>
                        <div class="col-md-6">
                            <div class="card card-custom p-4 h-100 shadow-xs border-top border-4 <?php echo ($t['urgency'] === 'Darurat') ? 'border-danger' : (($t['urgency'] === 'Tinggi') ? 'border-warning' : 'border-sage'); ?>">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-bold text-charcoal fs-7"><?php echo e($t['ticket_number']); ?></span>
                                    <?php echo renderUrgencyBadge($t['urgency']); ?>
                                </div>
                                <h6 class="fw-bold text-charcoal mb-1"><?php echo e($t['title']); ?></h6>
                                <p class="text-taupe fs-8 mb-2"><i class="bi bi-geo-alt me-1 text-sage"></i> Lokasi: <strong><?php echo e($t['location']); ?></strong></p>
                                
                                <div class="bg-light p-2.5 rounded-3 fs-8 text-charcoal mb-3" style="max-height: 80px; overflow: hidden; text-overflow: ellipsis;">
                                    <?php echo e($t['description']); ?>
                                </div>

                                <div class="mt-auto pt-2 border-top d-flex align-items-center justify-content-between">
                                    <small class="text-taupe fs-8">Pelapor: <?php echo e($t['reporter_name']); ?></small>
                                    <a href="<?php echo BASE_URL; ?>/reports/detail.php?id=<?php echo (int)$t['id']; ?>" class="btn btn-outline-sage btn-sm rounded-pill px-3 py-1 fs-8">
                                        Periksa Detail <i class="bi bi-chevron-right ms-0.5"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
