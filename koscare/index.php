<?php
/**
 * KosCare - Halaman Utama (Landing Page)
 * Desain Pastel Lilac Aesthetic Minimalis
 */

define('KOSCARE_APP', true);

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'KosCare — Perawatan Kos Jadi Lebih Mudah';

require_once __DIR__ . '/includes/header.php';
?>

<div class="min-vh-100 d-flex flex-column justify-content-between">
    <!-- Navbar Minimalis -->
    <?php
    $dynLogo = function_exists('getAppLogo') ? getAppLogo() : null;
    $dynName = function_exists('getAppName') ? getAppName() : 'KosCare';
    $dynTagline = function_exists('getAppTagline') ? getAppTagline() : 'Perawatan Kos Jadi Lebih Mudah';
    $dynSettings = function_exists('getThemeSettings') ? getThemeSettings() : [];
    $dynLogoHeight = function_exists('getLogoHeightPx') ? getLogoHeightPx($dynSettings['logo_size'] ?? 'medium') : 36;
    ?>
    <header class="py-3 px-4">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="<?php echo BASE_URL; ?>/index.php" class="brand-title d-flex align-items-center gap-2">
                <?php if ($dynLogo): ?>
                    <img src="<?php echo htmlspecialchars($dynLogo); ?>" alt="Logo" style="height: <?php echo $dynLogoHeight; ?>px; max-width: 140px; object-fit: contain;">
                <?php else: ?>
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 36px; height: 36px; background-color: var(--color-pastel-lilac); color: var(--color-primary-purple);">
                        <i class="bi bi-house-heart"></i>
                    </span>
                <?php endif; ?>
                <span><?php echo htmlspecialchars($dynName); ?></span>
            </a>
            <div>
                <a href="<?php echo BASE_URL; ?>/register.php" class="text-charcoal text-decoration-none fw-semibold me-3">
                    Registrasi
                </a>
                <a href="<?php echo BASE_URL; ?>/login.php?role=penghuni" class="btn btn-primary-purple px-4 py-1.5 fs-7">
                    Masuk
                </a>
            </div>
        </div>
    </header>

    <!-- Konten Utama: 3 Pilihan Aksi Utama -->
    <main class="my-auto py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-7 text-center">
                    <!-- Logo / Identitas -->
                    <div class="mb-3">
                        <?php if ($dynLogo): ?>
                            <img src="<?php echo htmlspecialchars($dynLogo); ?>" alt="Logo" style="height: 72px; max-width: 180px; object-fit: contain;">
                        <?php else: ?>
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle shadow-xs" style="width: 72px; height: 72px; background: linear-gradient(135deg, var(--color-pastel-lilac), var(--color-primary-purple)); color: #FFFFFF; font-size: 2rem;">
                                <i class="bi bi-house-heart-fill"></i>
                            </span>
                        <?php endif; ?>
                    </div>

                    <h1 class="display-5 fw-bold text-charcoal mb-2" style="letter-spacing: -0.5px;">
                        <?php echo htmlspecialchars($dynName); ?>
                    </h1>

                    <p class="lead text-charcoal mb-5 fs-5" style="font-style: italic;">
                        “<?php echo htmlspecialchars($dynTagline); ?>”
                    </p>

                    <!-- Kartu Pilihan Utama yang Paling Menonjol -->
                    <div class="card card-aesthetic p-4 p-md-5 mb-4 shadow-sm text-start" style="background-color: #FFFFFF;">
                        <h4 class="text-center fw-bold text-charcoal mb-4">
                            Pilih Akses Masuk
                        </h4>

                        <div class="d-grid gap-3">
                            <!-- 1. Masuk sebagai Penghuni -->
                            <a href="<?php echo BASE_URL; ?>/login.php?role=penghuni" class="btn btn-primary-purple py-3 fs-6 shadow-xs">
                                <i class="bi bi-person-fill fs-5"></i>
                                <span>Masuk sebagai Penghuni</span>
                            </a>

                            <!-- 2. Masuk sebagai Pengelola -->
                            <a href="<?php echo BASE_URL; ?>/login.php?role=pengelola" class="btn btn-outline-purple py-3 fs-6">
                                <i class="bi bi-shield-check fs-5" style="color: #9B82D8;"></i>
                                <span>Masuk sebagai Pengelola</span>
                            </a>

                            <!-- 3. Registrasi Penghuni -->
                            <a href="<?php echo BASE_URL; ?>/register.php" class="btn btn-pastel-pink py-3 fs-6 text-charcoal">
                                <i class="bi bi-pencil-square fs-5"></i>
                                <span>Registrasi Penghuni Baru</span>
                            </a>
                        </div>
                    </div>

                    <p class="text-muted small mb-0">
                        Platform pelaporan kerusakan fasilitas yang rapi, tenang, dan transparan.
                    </p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer Aesthetic -->
    <footer class="footer-aesthetic text-center">
        <div class="container">
            <span class="text-charcoal small">
                &copy; <?php echo date('Y'); ?> KosCare. Seluruh hak cipta dilindungi.
            </span>
        </div>
    </footer>
</div>

</body>
</html>
