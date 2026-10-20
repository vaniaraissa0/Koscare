<?php
/**
 * KosCare - Pendaftaran Akun Penghuni
 * Desain Pastel Lilac Aesthetic
 */

define('KOSCARE_APP', true);

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    $role = currentRole();
    if ($role === 'pengelola') header('Location: ' . BASE_URL . '/dashboard/pengelola.php');
    elseif ($role === 'teknisi') header('Location: ' . BASE_URL . '/technician/dashboard.php');
    else header('Location: ' . BASE_URL . '/dashboard/penghuni.php');
    exit;
}

$errors = [];
$name = '';
$username = '';
$email = '';
$phone = '';
$roomNumber = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCSRF();

    $name = trim($_POST['name'] ?? '');
    $username = strtolower(trim($_POST['username'] ?? ''));
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $roomNumber = trim($_POST['room_number'] ?? '');

    if (empty($name)) {
        $errors['name'] = 'Nama lengkap wajib diisi.';
    } elseif (strlen($name) < 3 || strlen($name) > 100) {
        $errors['name'] = 'Nama harus antara 3 - 100 karakter.';
    }

    if (empty($username)) {
        $errors['username'] = 'Username wajib diisi.';
    } elseif (!preg_match('/^[a-z0-9._]{3,30}$/', $username)) {
        $errors['username'] = 'Username hanya boleh huruf kecil, angka, titik, dan underscore.';
    }

    if (empty($email)) {
        $errors['email'] = 'Email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email tidak valid.';
    }

    if (empty($phone)) {
        $errors['phone'] = 'Nomor telepon wajib diisi.';
    }

    if (empty($roomNumber)) {
        $errors['room_number'] = 'Nomor kamar wajib diisi.';
    }

    if (empty($password)) {
        $errors['password'] = 'Kata sandi wajib diisi.';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Kata sandi minimal 8 karakter.';
    }

    if ($password !== $passwordConfirm) {
        $errors['password_confirm'] = 'Konfirmasi kata sandi tidak cocok.';
    }

    if (empty($errors)) {
        try {
            $pdo = getDBConnection();
            $checkStmt = $pdo->prepare("SELECT username, email FROM users WHERE username = :username OR email = :email LIMIT 1");
            $checkStmt->execute(['username' => $username, 'email' => $email]);
            $existingUser = $checkStmt->fetch();

            if ($existingUser) {
                if ($existingUser['username'] === $username) {
                    $errors['username'] = 'Username sudah digunakan.';
                }
                if ($existingUser['email'] === $email) {
                    $errors['email'] = 'Email sudah terdaftar.';
                }
            }
        } catch (Exception $e) {
            $errors['general'] = 'Terjadi kesalahan sistem. Silakan coba kembali.';
        }
    }

    if (empty($errors)) {
        try {
            $pdo = getDBConnection();
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            
            $insertStmt = $pdo->prepare("
                INSERT INTO users (name, username, email, password, phone, room_number, role, status)
                VALUES (:name, :username, :email, :password, :phone, :room, 'penghuni', 'active')
            ");

            $insertStmt->execute([
                'name'     => $name,
                'username' => $username,
                'email'    => $email,
                'password' => $passwordHash,
                'phone'    => $phone,
                'room'     => $roomNumber
            ]);

            setFlash('success', 'Pendaftaran berhasil! Silakan masuk dengan akun Anda.');
            header('Location: ' . BASE_URL . '/login.php?role=penghuni');
            exit;

        } catch (Exception $e) {
            $errors['general'] = 'Gagal menyimpan pendaftaran.';
        }
    }
}

$pageTitle = 'Registrasi Penghuni — ' . APP_NAME;
$activeNav = 'register';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container py-5 my-4">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-7 col-xl-6">
            <div class="card card-aesthetic p-4 p-sm-5 shadow-sm">
                <!-- Header Form -->
                <div class="text-center mb-4">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow-xs" style="width: 56px; height: 56px; background-color: var(--color-pastel-pink); color: #8A332A; font-size: 1.5rem;">
                        <i class="bi bi-pencil-heart"></i>
                    </span>
                    <h3 class="fw-bold text-charcoal mb-1">Registrasi Penghuni</h3>
                    <p class="text-muted small">
                        Daftarkan kamar Anda untuk mulai melaporkan fasilitas dengan tenang.
                    </p>
                </div>

                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger rounded-4 py-2.5 small mb-4" style="background-color: #FCE8E6; border-color: #F8DCE8; color: #8A332A;">
                        <i class="bi bi-exclamation-circle-fill me-1"></i> <?php echo e($errors['general']); ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>/register.php" method="POST" novalidate>
                    <?php echo csrfField(); ?>

                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold small text-charcoal">Nama Lengkap *</label>
                        <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo e($name); ?>" placeholder="Nama lengkap Anda" required>
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback small"><?php echo e($errors['name']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label for="username" class="form-label fw-bold small text-charcoal">Username *</label>
                            <input type="text" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" id="username" name="username" value="<?php echo e($username); ?>" placeholder="username" required>
                            <?php if (isset($errors['username'])): ?>
                                <div class="invalid-feedback small"><?php echo e($errors['username']); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-sm-6">
                            <label for="room_number" class="form-label fw-bold small text-charcoal">Nomor Kamar *</label>
                            <input type="text" class="form-control <?php echo isset($errors['room_number']) ? 'is-invalid' : ''; ?>" id="room_number" name="room_number" value="<?php echo e($roomNumber); ?>" placeholder="Contoh: Kamar 102" required>
                            <?php if (isset($errors['room_number'])): ?>
                                <div class="invalid-feedback small"><?php echo e($errors['room_number']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label for="email" class="form-label fw-bold small text-charcoal">Email *</label>
                            <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo e($email); ?>" placeholder="nama@email.com" required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback small"><?php echo e($errors['email']); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-sm-6">
                            <label for="phone" class="form-label fw-bold small text-charcoal">No. Telepon / WA *</label>
                            <input type="tel" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" id="phone" name="phone" value="<?php echo e($phone); ?>" placeholder="081234567890" required>
                            <?php if (isset($errors['phone'])): ?>
                                <div class="invalid-feedback small"><?php echo e($errors['phone']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label for="password" class="form-label fw-bold small text-charcoal">Kata Sandi *</label>
                            <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" id="password" name="password" placeholder="Minimal 8 karakter" required>
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback small"><?php echo e($errors['password']); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-sm-6">
                            <label for="password_confirm" class="form-label fw-bold small text-charcoal">Ulangi Sandi *</label>
                            <input type="password" class="form-control <?php echo isset($errors['password_confirm']) ? 'is-invalid' : ''; ?>" id="password_confirm" name="password_confirm" placeholder="Ulangi kata sandi" required>
                            <?php if (isset($errors['password_confirm'])): ?>
                                <div class="invalid-feedback small"><?php echo e($errors['password_confirm']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary-purple py-2.5">
                            <i class="bi bi-check-circle"></i>
                            <span>Daftar Sekarang</span>
                        </button>
                    </div>

                    <div class="text-center pt-2">
                        <span class="text-muted small">Sudah memiliki akun? </span>
                        <a href="<?php echo BASE_URL; ?>/login.php" class="fw-bold text-decoration-none" style="color: var(--color-primary-purple);">
                            Masuk di Sini
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
