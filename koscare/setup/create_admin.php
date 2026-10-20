<?php
/**
 * KosCare - Skrip Setup Administrator Satu Kali (One-Time Setup)
 * Membuat akun Pengelola awal: Vania Raissa (username: vania.raissa)
 */

define('KOSCARE_APP', true);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$lockFile = __DIR__ . '/setup.lock';
$message = '';
$status = 'info';

// 1. Cek apakah setup sudah pernah dikunci
if (file_exists($lockFile)) {
    $status = 'warning';
    $message = 'Skrip setup administrator telah DIKUNCI demi alasan keamanan (ditemukan file setup.lock). Jika Anda perlu menjalankan setup ulang, hapus file setup.lock secara manual dari folder setup/.';
} else {
    try {
        $pdo = getDBConnection();

        $adminName = 'Vania Raissa';
        $adminUsername = 'vania.raissa';
        $adminEmail = 'vania.raissa@koscare.local';
        $adminPhone = '081234567890';
        $adminRoom = 'Kantor Pengelola';
        $adminPasswordPlain = 'VaniaR@1';

        // Hash password menggunakan BCRYPT
        $passwordHash = password_hash($adminPasswordPlain, PASSWORD_BCRYPT);

        // Cek apakah akun pengelola ini sudah ada
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $adminUsername, 'email' => $adminEmail]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Update password & status
            $update = $pdo->prepare("UPDATE users SET password = :password, role = 'pengelola', status = 'active' WHERE id = :id");
            $update->execute(['password' => $passwordHash, 'id' => $existing['id']]);
            $status = 'success';
            $message = "Akun Pengelola <strong>{$adminName}</strong> ({$adminUsername}) sudah ada dan kata sandinya berhasil diperbarui.";
        } else {
            // Insert baru
            $insert = $pdo->prepare("
                INSERT INTO users (name, username, email, password, phone, room_number, role, status)
                VALUES (:name, :username, :email, :password, :phone, :room, 'pengelola', 'active')
            ");
            $insert->execute([
                'name'     => $adminName,
                'username' => $adminUsername,
                'email'    => $adminEmail,
                'password' => $passwordHash,
                'phone'    => $adminPhone,
                'room'     => $adminRoom
            ]);
            $status = 'success';
            $message = "Akun Pengelola <strong>{$adminName}</strong> ({$adminUsername}) berhasil dibuat secara aman!";
        }

        // Kunci setup secara otomatis dengan membuat file setup.lock
        file_put_contents($lockFile, "Setup locked at " . date('Y-m-d H:i:s') . " by " . ($_SERVER['REMOTE_ADDR'] ?? 'local'));
        
        // Log aktivitas
        logActivity(null, 'ADMIN_SETUP', "Inisialisasi akun pengelola vania.raissa berhasil");

    } catch (Exception $e) {
        $status = 'danger';
        $message = 'Gagal membuat akun pengelola: ' . htmlspecialchars($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Pengelola — KosCare</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body class="bg-ivory py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <div class="card card-custom p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="brand-icon mx-auto mb-3" style="width: 56px; height: 56px; font-size: 1.6rem;">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <h4 class="fw-bold text-charcoal mb-1">Inisialisasi Akun Pengelola</h4>
                        <p class="text-taupe fs-7">Skrip Setup Sekali Pakai KosCare (XAMPP)</p>
                    </div>

                    <div class="alert alert-<?php echo $status; ?> rounded-4 mb-4" role="alert">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi <?php echo ($status === 'success') ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill'; ?> fs-5"></i>
                            <h6 class="mb-0 fw-bold"><?php echo ($status === 'success') ? 'Setup Berhasil' : 'Pemberitahuan'; ?></h6>
                        </div>
                        <p class="mb-0 fs-7"><?php echo $message; ?></p>
                    </div>

                    <?php if ($status === 'success'): ?>
                        <div class="bg-sage-light p-3 rounded-4 mb-4 border">
                            <h6 class="fw-bold text-charcoal mb-2"><i class="bi bi-key-fill text-sage me-1"></i> Kredensial Awal Pengelola:</h6>
                            <ul class="list-unstyled mb-0 fs-7">
                                <li><strong>Nama:</strong> Vania Raissa</li>
                                <li><strong>Username:</strong> <code>vania.raissa</code></li>
                                <li><strong>Peran:</strong> Pengelola</li>
                            </ul>
                            <small class="text-taupe mt-2 d-block">
                                * Kata sandi yang diminta telah di-hash dengan aman. Segera ganti kata sandi setelah Anda masuk pertama kali.
                            </small>
                        </div>

                        <div class="bg-pink-light p-3 rounded-4 mb-4 border border-danger-subtle">
                            <strong class="text-danger d-block mb-1"><i class="bi bi-shield-exclamation me-1"></i> Tindakan Keamanan Wajib:</strong>
                            <p class="fs-8 text-taupe mb-0">
                                File kunci <code>setup/setup.lock</code> telah dibuat otomatis. Untuk keamanan maksimal, Anda dianjurkan untuk menghapus file <code>create_admin.php</code> atau menghapus seluruh folder <code>setup/</code> dari instalasi XAMPP Anda.
                            </p>
                        </div>
                    <?php endif; ?>

                    <div class="d-grid gap-2">
                        <a href="<?php echo BASE_URL; ?>/login.php" class="btn btn-sage py-2.5 rounded-pill fw-semibold text-white">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Menuju Halaman Login
                        </a>
                        <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-outline-sage py-2 rounded-pill">
                            Kembali ke Halaman Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
