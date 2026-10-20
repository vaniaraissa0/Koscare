<?php
/**
 * KosCare - Profil Pengguna & Ganti Kata Sandi
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
$userId = (int)$user['id'];
$pdo = getDBConnection();

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $userId]);
$userData = $stmt->fetch();

if (!$userData) {
    logoutUser();
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

$profileErrors = [];
$passwordErrors = [];

// Penanganan Form Update Profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    requireCSRF();

    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = trim($_POST['phone'] ?? '');
    $roomNumber = trim($_POST['room_number'] ?? '');

    if (empty($name) || strlen($name) < 3) {
        $profileErrors['name'] = 'Nama lengkap minimal 3 karakter.';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $profileErrors['email'] = 'Format email tidak valid.';
    } else {
        $chk = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id LIMIT 1");
        $chk->execute(['email' => $email, 'id' => $userId]);
        if ($chk->fetch()) {
            $profileErrors['email'] = 'Alamat email sudah digunakan.';
        }
    }

    if (empty($phone)) {
        $profileErrors['phone'] = 'Nomor telepon wajib diisi.';
    }

    if (empty($profileErrors)) {
        try {
            $upStmt = $pdo->prepare("
                UPDATE users 
                SET name = :name, email = :email, phone = :phone, room_number = :room, updated_at = NOW()
                WHERE id = :id
            ");
            $upStmt->execute([
                'name'  => $name,
                'email' => $email,
                'phone' => $phone,
                'room'  => $roomNumber,
                'id'    => $userId
            ]);

            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_phone'] = $phone;
            $_SESSION['user_room_number'] = $roomNumber;

            setFlash('success', 'Profil Anda berhasil diperbarui.');
            header('Location: ' . BASE_URL . '/profile/index.php');
            exit;

        } catch (Exception $e) {
            $profileErrors['general'] = 'Gagal menyimpan profil.';
        }
    }
}

// Penanganan Form Ubah Sandi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    requireCSRF();

    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($currentPassword)) {
        $passwordErrors['current_password'] = 'Kata sandi saat ini wajib diisi.';
    } elseif (!password_verify($currentPassword, $userData['password'])) {
        $passwordErrors['current_password'] = 'Kata sandi saat ini tidak sesuai.';
    }

    if (empty($newPassword) || strlen($newPassword) < 8) {
        $passwordErrors['new_password'] = 'Kata sandi baru minimal 8 karakter.';
    }

    if ($newPassword !== $confirmPassword) {
        $passwordErrors['confirm_password'] = 'Konfirmasi kata sandi tidak cocok.';
    }

    if (empty($passwordErrors)) {
        try {
            $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
            $upPass = $pdo->prepare("UPDATE users SET password = :pass, updated_at = NOW() WHERE id = :id");
            $upPass->execute(['pass' => $newHash, 'id' => $userId]);

            setFlash('success', 'Kata sandi Anda berhasil diperbarui.');
            header('Location: ' . BASE_URL . '/profile/index.php');
            exit;

        } catch (Exception $e) {
            $passwordErrors['general'] = 'Gagal memperbarui kata sandi.';
        }
    }
}

$pageTitle = 'Profil Pengguna — ' . APP_NAME;
$activeNav = 'profile';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-4 my-2">
    <?php renderFlash(); ?>

    <div class="row g-4">
        <!-- Form Update Profil -->
        <div class="col-md-6">
            <div class="card card-aesthetic p-4 p-md-5 h-100 shadow-sm">
                <h4 class="fw-bold text-charcoal mb-3 pb-2 border-bottom">
                    Informasi Profil
                </h4>

                <?php if (isset($profileErrors['general'])): ?>
                    <div class="alert alert-danger small py-2 rounded-3"><?php echo e($profileErrors['general']); ?></div>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>/profile/index.php" method="POST">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="action" value="update_profile">

                    <div class="mb-3">
                        <label class="form-label small text-muted">Username</label>
                        <input type="text" class="form-control bg-light" value="<?php echo e($userData['username']); ?>" readonly disabled>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold small text-charcoal">Nama Lengkap *</label>
                        <input type="text" class="form-control <?php echo isset($profileErrors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" value="<?php echo e($userData['name']); ?>" required>
                        <?php if (isset($profileErrors['name'])): ?>
                            <div class="invalid-feedback small"><?php echo e($profileErrors['name']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold small text-charcoal">Email *</label>
                        <input type="email" class="form-control <?php echo isset($profileErrors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo e($userData['email']); ?>" required>
                        <?php if (isset($profileErrors['email'])): ?>
                            <div class="invalid-feedback small"><?php echo e($profileErrors['email']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label fw-bold small text-charcoal">No. Telepon / WA *</label>
                        <input type="text" class="form-control <?php echo isset($profileErrors['phone']) ? 'is-invalid' : ''; ?>" id="phone" name="phone" value="<?php echo e($userData['phone']); ?>" required>
                        <?php if (isset($profileErrors['phone'])): ?>
                            <div class="invalid-feedback small"><?php echo e($profileErrors['phone']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="room_number" class="form-label fw-bold small text-charcoal">Nomor Kamar</label>
                        <input type="text" class="form-control" id="room_number" name="room_number" value="<?php echo e($userData['room_number']); ?>" placeholder="Kamar 102">
                    </div>

                    <button type="submit" class="btn btn-primary-purple px-4 py-2">
                        Simpan Profil
                    </button>
                </form>
            </div>
        </div>

        <!-- Form Ubah Kata Sandi -->
        <div class="col-md-6">
            <div class="card card-aesthetic p-4 p-md-5 h-100 shadow-sm">
                <h4 class="fw-bold text-charcoal mb-3 pb-2 border-bottom">
                    Ganti Kata Sandi
                </h4>

                <?php if (isset($passwordErrors['general'])): ?>
                    <div class="alert alert-danger small py-2 rounded-3"><?php echo e($passwordErrors['general']); ?></div>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>/profile/index.php" method="POST">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="action" value="change_password">

                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-bold small text-charcoal">Kata Sandi Saat Ini *</label>
                        <input type="password" class="form-control <?php echo isset($passwordErrors['current_password']) ? 'is-invalid' : ''; ?>" id="current_password" name="current_password" placeholder="Masukkan kata sandi lama" required>
                        <?php if (isset($passwordErrors['current_password'])): ?>
                            <div class="invalid-feedback small"><?php echo e($passwordErrors['current_password']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label fw-bold small text-charcoal">Kata Sandi Baru (Min 8 Karakter) *</label>
                        <input type="password" class="form-control <?php echo isset($passwordErrors['new_password']) ? 'is-invalid' : ''; ?>" id="new_password" name="new_password" placeholder="Minimal 8 karakter" required>
                        <?php if (isset($passwordErrors['new_password'])): ?>
                            <div class="invalid-feedback small"><?php echo e($passwordErrors['new_password']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="confirm_password" class="form-label fw-bold small text-charcoal">Konfirmasi Kata Sandi Baru *</label>
                        <input type="password" class="form-control <?php echo isset($passwordErrors['confirm_password']) ? 'is-invalid' : ''; ?>" id="confirm_password" name="confirm_password" placeholder="Ulangi kata sandi baru" required>
                        <?php if (isset($passwordErrors['confirm_password'])): ?>
                            <div class="invalid-feedback small"><?php echo e($passwordErrors['confirm_password']); ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-outline-purple px-4 py-2">
                        Perbarui Kata Sandi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
