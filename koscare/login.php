<?php
/**
 * KosCare - Halaman Masuk (Login)
 * Desain Pastel Lilac Aesthetic
 */

define('KOSCARE_APP', true);

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

// Jika sudah masuk, arahkan ke dashboard yang sesuai
if (isLoggedIn()) {
    $role = currentRole();
    if ($role === 'pengelola') header('Location: ' . BASE_URL . '/dashboard/pengelola.php');
    elseif ($role === 'teknisi') header('Location: ' . BASE_URL . '/technician/dashboard.php');
    else header('Location: ' . BASE_URL . '/dashboard/penghuni.php');
    exit;
}

$roleHint = $_GET['role'] ?? '';
$error = '';
$identity = '';

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

if (time() - $_SESSION['last_attempt_time'] > 300) {
    $_SESSION['login_attempts'] = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCSRF();

    if ($_SESSION['login_attempts'] >= 5) {
        $remaining = 300 - (time() - $_SESSION['last_attempt_time']);
        $error = "Terlalu banyak percobaan masuk yang tidak sesuai. Mohon tunggu {$remaining} detik.";
    } else {
        $identity = trim($_POST['identity'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($identity) || empty($password)) {
            $error = 'Silakan masukkan username/email dan kata sandi Anda.';
        } else {
            try {
                $pdo = getDBConnection();
                $stmt = $pdo->prepare("
                    SELECT id, name, username, email, password, role, room_number, phone, status
                    FROM users
                    WHERE (username = :id1 OR email = :id2)
                    LIMIT 1
                ");
                $stmt->execute(['id1' => $identity, 'id2' => $identity]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    if ($user['status'] !== 'active') {
                        $error = 'Akun sedang tidak aktif. Silakan hubungi pengelola.';
                    } else {
                        $_SESSION['login_attempts'] = 0;
                        loginUser($user);

                        if ($user['role'] === 'pengelola') {
                            header('Location: ' . BASE_URL . '/dashboard/pengelola.php');
                        } elseif ($user['role'] === 'teknisi') {
                            header('Location: ' . BASE_URL . '/technician/dashboard.php');
                        } else {
                            header('Location: ' . BASE_URL . '/dashboard/penghuni.php');
                        }
                        exit;
                    }
                } else {
                    $_SESSION['login_attempts']++;
                    $_SESSION['last_attempt_time'] = time();
                    $error = 'Username, email, atau kata sandi tidak sesuai.';
                }
            } catch (Exception $e) {
                $error = 'Maaf, sistem sedang sibuk. Silakan coba sesaat lagi.';
            }
        }
    }
}

$pageTitle = 'Masuk — ' . APP_NAME;
$activeNav = 'login';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container py-5 my-4">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card card-aesthetic p-4 p-sm-5 shadow-sm">
                <!-- Header Card -->
                <div class="text-center mb-4">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow-xs" style="width: 56px; height: 56px; background-color: var(--color-light-lilac); color: var(--color-primary-purple); font-size: 1.5rem;">
                        <i class="bi bi-person-heart"></i>
                    </span>
                    <h3 class="fw-bold text-charcoal mb-1">Masuk ke KosCare</h3>
                    <p class="text-muted small">
                        <?php if ($roleHint === 'pengelola'): ?>
                            Portal Masuk Pengelola Kos
                        <?php elseif ($roleHint === 'penghuni'): ?>
                            Portal Masuk Penghuni Kos
                        <?php else: ?>
                            Silakan masukkan akun Anda
                        <?php endif; ?>
                    </p>
                </div>

                <?php renderFlash(); ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger rounded-4 py-2.5 small mb-4 d-flex align-items-center gap-2" style="background-color: #FCE8E6; border-color: #F8DCE8; color: #8A332A;">
                        <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                        <div><?php echo e($error); ?></div>
                    </div>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>/login.php<?php echo !empty($roleHint) ? '?role=' . e($roleHint) : ''; ?>" method="POST">
                    <?php echo csrfField(); ?>

                    <div class="mb-3">
                        <label for="identity" class="form-label fw-bold small text-charcoal">Username atau Email</label>
                        <input type="text" class="form-control" id="identity" name="identity" value="<?php echo e($identity); ?>" placeholder="Masukkan username atau email" required autofocus>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold small text-charcoal">Kata Sandi</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan kata sandi" required>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary-purple py-2.5">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>Masuk</span>
                        </button>
                    </div>

                    <div class="text-center pt-3 border-top">
                        <span class="text-muted small">Belum memiliki akun penghuni? </span>
                        <a href="<?php echo BASE_URL; ?>/register.php" class="fw-bold text-decoration-none" style="color: var(--color-primary-purple);">
                            Registrasi di Sini
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
