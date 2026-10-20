<?php
/**
 * KosCare - Detail Laporan Kerusakan
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

$reportId = (int)($_GET['id'] ?? 0);
if ($reportId <= 0) {
    header('Location: ' . BASE_URL . ($isManager ? '/dashboard/pengelola.php' : '/dashboard/penghuni.php'));
    exit;
}

$stmt = $pdo->prepare("
    SELECT r.*, u.name AS reporter_name, u.username AS reporter_username, u.email AS reporter_email, 
           u.phone AS reporter_phone, u.room_number AS reporter_room
    FROM reports r
    JOIN users u ON r.user_id = u.id
    WHERE r.id = :id
    LIMIT 1
");
$stmt->execute(['id' => $reportId]);
$report = $stmt->fetch();

if (!$report) {
    header('Location: ' . BASE_URL . ($isManager ? '/dashboard/pengelola.php' : '/dashboard/penghuni.php'));
    exit;
}

if (!$isManager && (int)$report['user_id'] !== (int)$user['id']) {
    setFlash('danger', 'Akses ditolak.');
    header('Location: ' . BASE_URL . '/dashboard/penghuni.php');
    exit;
}

// Tangani Komentar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_comment') {
    requireCSRF();
    $commentText = trim($_POST['comment'] ?? '');

    if (!empty($commentText)) {
        try {
            $stmtC = $pdo->prepare("INSERT INTO report_comments (report_id, user_id, comment, created_at) VALUES (:rid, :uid, :comm, NOW())");
            $stmtC->execute([
                'rid'  => $reportId,
                'uid'  => $user['id'],
                'comm' => $commentText
            ]);

            setFlash('success', 'Pesan berhasil dikirim.');
            header("Location: " . BASE_URL . "/reports/detail.php?id={$reportId}#diskusi");
            exit;
        } catch (Exception $e) {}
    }
}

// Ambil Riwayat Status
$stmtHist = $pdo->prepare("
    SELECT h.*, u.name AS changer_name, u.role AS changer_role
    FROM report_status_history h
    JOIN users u ON h.changed_by_user_id = u.id
    WHERE h.report_id = :rid
    ORDER BY h.created_at ASC
");
$stmtHist->execute(['rid' => $reportId]);
$history = $stmtHist->fetchAll();

// Ambil Komentar
$stmtComm = $pdo->prepare("
    SELECT c.*, u.name AS author_name, u.role AS author_role
    FROM report_comments c
    JOIN users u ON c.user_id = u.id
    WHERE c.report_id = :rid
    ORDER BY c.created_at ASC
");
$stmtComm->execute(['rid' => $reportId]);
$comments = $stmtComm->fetchAll();

// Ambil Catatan Perbaikan
$stmtRepair = $pdo->prepare("SELECT * FROM repair_records WHERE report_id = :rid LIMIT 1");
$stmtRepair->execute(['rid' => $reportId]);
$repairRecord = $stmtRepair->fetch();

$pageTitle = 'Detail Laporan — ' . APP_NAME;
$activeNav = $isManager ? 'all_reports' : 'my_reports';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-4 my-2">
    <?php renderFlash(); ?>

    <div class="mb-3">
        <a href="<?php echo BASE_URL . ($isManager ? '/dashboard/pengelola.php' : '/dashboard/penghuni.php'); ?>" class="btn btn-outline-purple btn-sm px-3">
            ← Kembali ke Dashboard
        </a>
    </div>

    <div class="row g-4">
        <!-- Kolom Kiri: Detail Laporan, Foto, Timeline, Komentar -->
        <div class="col-lg-8">
            <div class="card card-aesthetic p-4 p-md-5 mb-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-start pb-3 mb-3 border-bottom">
                    <div>
                        <!-- Nomor tiket kecil dan sederhana sesuai aturan 9 -->
                        <small class="text-muted d-block" style="font-size: 0.75rem;">No. Laporan: <?php echo e($report['ticket_number']); ?></small>
                        <h3 class="fw-bold text-charcoal mb-0"><?php echo e($report['title']); ?></h3>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <?php echo renderUrgencyBadge($report['urgency']); ?>
                        <?php echo renderStatusBadge($report['status']); ?>
                    </div>
                </div>

                <div class="row g-3 p-3 bg-light-lilac rounded-3 mb-4 text-charcoal small">
                    <div class="col-6 col-md-3">
                        <span class="text-muted d-block">Kategori</span>
                        <span class="fw-bold"><?php echo e($report['category']); ?></span>
                    </div>
                    <div class="col-6 col-md-3">
                        <span class="text-muted d-block">Lokasi</span>
                        <span class="fw-bold"><?php echo e($report['location']); ?></span>
                    </div>
                    <div class="col-6 col-md-3">
                        <span class="text-muted d-block">Pelapor</span>
                        <span class="fw-bold"><?php echo e($report['reporter_name']); ?></span>
                    </div>
                    <div class="col-6 col-md-3">
                        <span class="text-muted d-block">Tanggal</span>
                        <span class="fw-bold"><?php echo formatDateShort($report['created_at']); ?></span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-charcoal">Deskripsi Kerusakan:</label>
                    <div class="p-3 bg-white border rounded-3 text-charcoal small" style="line-height: 1.6; white-space: pre-wrap;"><?php echo e($report['description']); ?></div>
                </div>

                <?php if (!empty($report['photo'])): ?>
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-charcoal">Foto Dokumentasi Kerusakan:</label>
                        <div class="border rounded-3 p-2 bg-white d-inline-block shadow-xs" style="max-width: 400px;">
                            <img src="<?php echo UPLOAD_URL . e($report['photo']); ?>" alt="Foto Kerusakan" class="img-fluid rounded-2" style="max-height: 280px; width: 100%; object-fit: contain;">
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($report['status'] === 'Selesai' && $repairRecord): ?>
                    <div class="p-4 rounded-3 mb-4" style="background-color: #E2F0D9; color: #2B542C;">
                        <h6 class="fw-bold mb-2">Hasil Perbaikan Selesai</h6>
                        <ul class="list-unstyled mb-0 small">
                            <li class="mb-1"><strong>Tanggal Selesai:</strong> <?php echo formatDateIndo($repairRecord['completion_date']); ?></li>
                            <li class="mb-1"><strong>Teknisi:</strong> <?php echo e($repairRecord['technician_name']); ?></li>
                            <li class="mb-1"><strong>Tindakan:</strong> <?php echo e($repairRecord['repair_notes']); ?></li>
                            <?php if ((float)$repairRecord['cost_estimate'] > 0): ?>
                                <li><strong>Estimasi Biaya:</strong> Rp <?php echo number_format($repairRecord['cost_estimate'], 0, ',', '.'); ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($isManager): ?>
                    <div class="pt-3 border-top d-flex justify-content-end">
                        <form action="<?php echo BASE_URL; ?>/reports/delete.php" method="POST" onsubmit="return confirm('Hapus laporan ini?');">
                            <?php echo csrfField(); ?>
                            <input type="hidden" name="id" value="<?php echo (int)$report['id']; ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm px-3 rounded-pill">
                                Hapus Laporan
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Timeline Perkembangan Riwayat Status -->
            <div class="card card-aesthetic p-4 p-md-5 mb-4 shadow-sm">
                <h5 class="fw-bold text-charcoal mb-4 pb-2 border-bottom">
                    Riwayat Perkembangan Laporan
                </h5>

                <div class="timeline-track">
                    <?php foreach ($history as $h): ?>
                        <div class="timeline-item mb-4 position-relative">
                            <div class="timeline-dot"></div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <?php echo renderStatusBadge($h['new_status']); ?>
                                <small class="text-muted"><?php echo formatDateIndo($h['created_at']); ?></small>
                            </div>
                            <span class="small fw-bold text-charcoal">
                                Diperbarui oleh <?php echo e($h['changer_name']); ?> (<?php echo ucfirst(e($h['changer_role'])); ?>)
                            </span>
                            <?php if (!empty($h['notes'])): ?>
                                <p class="small text-charcoal mt-1 mb-0 p-2.5 rounded-3 bg-light-lilac">
                                    <?php echo e($h['notes']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Diskusi & Informasi -->
            <div id="diskusi" class="card card-aesthetic p-4 p-md-5 shadow-sm">
                <h5 class="fw-bold text-charcoal mb-3 pb-2 border-bottom">
                    Tanggapan & Informasi
                </h5>

                <?php if (empty($comments)): ?>
                    <p class="text-muted small mb-3">Belum ada tanggapan untuk laporan ini.</p>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3 mb-4">
                        <?php foreach ($comments as $c): ?>
                            <div class="p-3 rounded-3 border <?php echo ($c['author_role'] === 'pengelola') ? 'bg-light-lilac' : 'bg-white'; ?>">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-bold small text-charcoal"><?php echo e($c['author_name']); ?> (<?php echo ucfirst(e($c['author_role'])); ?>)</span>
                                    <small class="text-muted"><?php echo formatDateIndo($c['created_at']); ?></small>
                                </div>
                                <p class="text-charcoal small mb-0"><?php echo e($c['comment']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>/reports/detail.php?id=<?php echo $reportId; ?>" method="POST">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="action" value="add_comment">

                    <div class="mb-3">
                        <textarea class="form-control" name="comment" rows="3" placeholder="Tulis tanggapan atau informasi tambahan..." required></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary-purple px-4 py-2 small">
                            Kirim Tanggapan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Kolom Kanan: Panel Ubah Status (Pengelola) & Kontak -->
        <div class="col-lg-4">
            <?php if ($isManager): ?>
                <div class="card card-aesthetic p-4 mb-4 shadow-sm">
                    <h5 class="fw-bold text-charcoal mb-3 pb-2 border-bottom">
                        Ubah Status Laporan
                    </h5>

                    <form action="<?php echo BASE_URL; ?>/reports/update_status.php" method="POST">
                        <?php echo csrfField(); ?>
                        <input type="hidden" name="report_id" value="<?php echo (int)$report['id']; ?>">

                        <div class="mb-3">
                            <label for="new_status_select" class="form-label fw-bold small text-charcoal">Status Baru *</label>
                            <select class="form-select" id="new_status_select" name="new_status" required>
                                <?php foreach (ALL_STATUSES as $st): ?>
                                    <option value="<?php echo e($st); ?>" <?php echo ($report['status'] === $st) ? 'selected' : ''; ?>>
                                        <?php echo e($st); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div id="completed_extra_fields" class="p-3 bg-light-lilac rounded-3 mb-3 border <?php echo ($report['status'] === 'Selesai') ? '' : 'd-none'; ?>">
                            <div class="mb-2">
                                <label for="completion_date" class="form-label small mb-1">Tanggal Selesai *</label>
                                <input type="date" class="form-control" id="completion_date" name="completion_date" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="mb-2">
                                <label for="technician_name" class="form-label small mb-1">Nama Teknisi</label>
                                <input type="text" class="form-control" id="technician_name" name="technician_name" value="<?php echo e($repairRecord['technician_name'] ?? 'Tim Perbaikan Kos'); ?>">
                            </div>
                            <div class="mb-0">
                                <label for="cost_estimate" class="form-label small mb-1">Estimasi Biaya (Rp)</label>
                                <input type="number" class="form-control" id="cost_estimate" name="cost_estimate" value="<?php echo (int)($repairRecord['cost_estimate'] ?? 0); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label id="status_notes_label" for="status_notes" class="form-label fw-bold small text-charcoal">Catatan Perubahan</label>
                            <textarea class="form-control" id="status_notes" name="notes" rows="3" placeholder="Tambahkan keterangan..."></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary-purple py-2">
                                Simpan Status Baru
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <div class="card card-aesthetic p-4 shadow-sm">
                <h5 class="fw-bold text-charcoal mb-3 pb-2 border-bottom">
                    Kontak Penghuni
                </h5>
                <ul class="list-unstyled mb-0 small text-charcoal">
                    <li class="mb-2">
                        <span class="text-muted d-block">Nama Lengkap</span>
                        <strong><?php echo e($report['reporter_name']); ?></strong>
                    </li>
                    <li class="mb-2">
                        <span class="text-muted d-block">Nomor Kamar</span>
                        <span><?php echo e($report['reporter_room']); ?></span>
                    </li>
                    <li class="mb-2">
                        <span class="text-muted d-block">No. Telepon / WA</span>
                        <span><?php echo e($report['reporter_phone']); ?></span>
                    </li>
                    <li>
                        <span class="text-muted d-block">Email</span>
                        <span><?php echo e($report['reporter_email']); ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
