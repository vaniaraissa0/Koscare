<?php
/**
 * KosCare - Formulir Pembuatan Laporan Kerusakan
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
$pdo = getDBConnection();

$errors = [];
$title = '';
$category = '';
$location = $user['room_number'] ?? '';
$description = '';
$urgency = 'Sedang';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCSRF();

    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $urgency = trim($_POST['urgency'] ?? 'Sedang');

    if (empty($title)) {
        $errors['title'] = 'Judul laporan wajib diisi.';
    } elseif (strlen($title) < 5 || strlen($title) > 150) {
        $errors['title'] = 'Judul laporan harus antara 5 hingga 150 karakter.';
    }

    if (empty($category)) {
        $errors['category'] = 'Pilih kategori kerusakan.';
    }

    if (empty($location)) {
        $errors['location'] = 'Lokasi atau nomor kamar wajib dicantumkan.';
    }

    if (empty($description)) {
        $errors['description'] = 'Deskripsi masalah kerusakan wajib diisi.';
    } elseif (strlen($description) < 10) {
        $errors['description'] = 'Berikan penjelasan lebih detail (minimal 10 karakter).';
    }

    if (!in_array($urgency, ALL_URGENCIES, true)) {
        $errors['urgency'] = 'Tingkat urgensi tidak valid.';
    }

    $uploadedFilename = null;
    $targetFilePath = null;

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['photo'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors['photo'] = 'Terjadi kesalahan saat mengunggah foto.';
        } else {
            if ($file['size'] > MAX_FILE_SIZE) {
                $errors['photo'] = 'Ukuran foto maksimal 2 MB.';
            }

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ALLOWED_EXTENSIONS, true)) {
                $errors['photo'] = 'Format foto harus JPG, JPEG, PNG, atau WebP.';
            }

            if (empty($errors['photo'])) {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($file['tmp_name']);

                if (!in_array($mimeType, ALLOWED_MIME_TYPES, true)) {
                    $errors['photo'] = 'Tipe file tidak valid.';
                }
            }

            if (empty($errors['photo'])) {
                if (!is_dir(UPLOAD_DIR)) {
                    mkdir(UPLOAD_DIR, 0755, true);
                }

                $uploadedFilename = bin2hex(random_bytes(16)) . '.' . $ext;
                $targetFilePath = UPLOAD_DIR . $uploadedFilename;

                if (!move_uploaded_file($file['tmp_name'], $targetFilePath)) {
                    $errors['photo'] = 'Gagal menyimpan foto.';
                    $uploadedFilename = null;
                }
            }
        }
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $ticketNumber = generateTicketNumber($pdo);
            $initialStatus = STATUS_WAITING;

            $stmt = $pdo->prepare("
                INSERT INTO reports (
                    ticket_number, user_id, title, category, location, 
                    description, urgency, photo, status, created_at
                ) VALUES (
                    :ticket, :uid, :title, :category, :location,
                    :description, :urgency, :photo, :status, NOW()
                )
            ");

            $stmt->execute([
                'ticket'      => $ticketNumber,
                'uid'         => $user['id'],
                'title'       => $title,
                'category'    => $category,
                'location'    => $location,
                'description' => $description,
                'urgency'     => $urgency,
                'photo'       => $uploadedFilename,
                'status'      => $initialStatus
            ]);

            $reportId = (int)$pdo->lastInsertId();

            $histStmt = $pdo->prepare("
                INSERT INTO report_status_history (
                    report_id, changed_by_user_id, previous_status, new_status, notes, created_at
                ) VALUES (
                    :rid, :uid, 'Baru Dibuat', :new_status, 'Laporan kerusakan fasilitas telah diajukan penghuni.', NOW()
                )
            ");
            $histStmt->execute([
                'rid'        => $reportId,
                'uid'        => $user['id'],
                'new_status' => $initialStatus
            ]);

            logActivity($user['id'], 'CREATE_REPORT', "Laporan dibuat");
            $pdo->commit();

            setFlash('success', 'Laporan Anda berhasil dikirimkan ke pengelola kos.');
            header('Location: ' . BASE_URL . '/reports/detail.php?id=' . $reportId);
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            if ($targetFilePath && file_exists($targetFilePath)) {
                unlink($targetFilePath);
            }
            $errors['general'] = 'Gagal menyimpan laporan. Silakan coba kembali.';
        }
    }
}

$pageTitle = 'Buat Laporan — ' . APP_NAME;
$activeNav = 'create_report';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-4 my-2">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?php renderFlash(); ?>

            <div class="card card-aesthetic p-4 p-md-5 shadow-sm">
                <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
                    <div>
                        <h3 class="fw-bold text-charcoal mb-1">Buat Laporan Kerusakan</h3>
                        <p class="text-muted small mb-0">Isi detail fasilitas yang membutuhkan penanganan.</p>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/dashboard/penghuni.php" class="btn btn-outline-purple btn-sm px-3">
                        Kembali
                    </a>
                </div>

                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger rounded-4 py-2.5 small mb-4" style="background-color: #FCE8E6; border-color: #F8DCE8; color: #8A332A;">
                        <?php echo e($errors['general']); ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>/reports/create.php" method="POST" enctype="multipart/form-data">
                    <?php echo csrfField(); ?>

                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold small text-charcoal">Judul Laporan *</label>
                        <input type="text" class="form-control <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>" id="title" name="title" value="<?php echo e($title); ?>" placeholder="Contoh: Kran Air Wastafel Kamar Mandi Bocor" required>
                        <?php if (isset($errors['title'])): ?>
                            <div class="invalid-feedback small"><?php echo e($errors['title']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="category" class="form-label fw-bold small text-charcoal">Kategori Kerusakan *</label>
                            <select class="form-select <?php echo isset($errors['category']) ? 'is-invalid' : ''; ?>" id="category" name="category" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach (ALL_CATEGORIES as $cat): ?>
                                    <option value="<?php echo e($cat); ?>" <?php echo ($category === $cat) ? 'selected' : ''; ?>>
                                        <?php echo e($cat); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['category'])): ?>
                                <div class="invalid-feedback small"><?php echo e($errors['category']); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="location" class="form-label fw-bold small text-charcoal">Lokasi Kerusakan *</label>
                            <input type="text" class="form-control <?php echo isset($errors['location']) ? 'is-invalid' : ''; ?>" id="location" name="location" value="<?php echo e($location); ?>" placeholder="Contoh: Kamar 102" required>
                            <?php if (isset($errors['location'])): ?>
                                <div class="invalid-feedback small"><?php echo e($errors['location']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-charcoal d-block">Tingkat Urgensi *</label>
                        <div class="row g-2">
                            <?php foreach (['Rendah', 'Sedang', 'Tinggi', 'Darurat'] as $urg): ?>
                                <div class="col-6 col-sm-3">
                                    <label class="card p-2 text-center rounded-3 border cursor-pointer h-100 <?php echo ($urgency === $urg) ? 'bg-light-lilac' : 'bg-white'; ?>">
                                        <input type="radio" name="urgency" value="<?php echo $urg; ?>" class="form-check-input mx-auto mb-1" <?php echo ($urgency === $urg) ? 'checked' : ''; ?>>
                                        <span class="small fw-bold text-charcoal"><?php echo $urg; ?></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold small text-charcoal">Deskripsi *</label>
                        <textarea class="form-control <?php echo isset($errors['description']) ? 'is-invalid' : ''; ?>" id="description" name="description" rows="4" placeholder="Jelaskan detail kendala yang dialami..." required><?php echo e($description); ?></textarea>
                        <?php if (isset($errors['description'])): ?>
                            <div class="invalid-feedback small"><?php echo e($errors['description']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="report_photo" class="form-label fw-bold small text-charcoal">Foto Kerusakan (Opsional)</label>
                        <input type="file" class="form-control <?php echo isset($errors['photo']) ? 'is-invalid' : ''; ?>" id="report_photo" name="photo" accept=".jpg,.jpeg,.png,.webp">
                        <small class="text-muted d-block mt-1">Format: JPG, JPEG, PNG, WebP (Maksimal 2 MB).</small>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="<?php echo BASE_URL; ?>/dashboard/penghuni.php" class="btn btn-outline-purple px-4 py-2">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary-purple px-5 py-2">
                            Kirim Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
