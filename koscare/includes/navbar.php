<?php
/**
 * KosCare - Navbar Utama
 * Desain Pastel Lilac Aesthetic
 */
defined('KOSCARE_APP') or define('KOSCARE_APP', true);

$user = currentUser();
$isLogged = isLoggedIn();
?>
<nav class="navbar navbar-expand-lg navbar-custom sticky-top py-2.5">
    <div class="container-fluid px-lg-4">
        <!-- Brand Logo -->
        <?php
        $dynLogo = function_exists('getAppLogo') ? getAppLogo() : null;
        $dynName = function_exists('getAppName') ? getAppName() : 'KosCare';
        $dynSettings = function_exists('getThemeSettings') ? getThemeSettings() : [];
        $dynLogoHeight = function_exists('getLogoHeightPx') ? getLogoHeightPx($dynSettings['logo_size'] ?? 'medium') : 36;
        ?>
        <a class="navbar-brand d-flex align-items-center gap-2 brand-title" href="<?php echo BASE_URL; ?>/index.php">
            <?php if ($dynLogo): ?>
                <img src="<?php echo htmlspecialchars($dynLogo); ?>" alt="Logo" style="height: <?php echo $dynLogoHeight; ?>px; max-width: 140px; object-fit: contain;">
            <?php else: ?>
                <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 36px; height: 36px; background-color: var(--color-pastel-lilac); color: var(--color-primary-purple);">
                    <i class="bi bi-house-heart"></i>
                </span>
            <?php endif; ?>
            <span><?php echo htmlspecialchars($dynName); ?></span>
        </a>

        <!-- Mobile Toggler -->
        <button class="navbar-toggler border-0 shadow-none p-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list fs-3 text-charcoal"></i>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-2 mt-3 mt-lg-0">
                <?php if ($isLogged): ?>
                    <?php if ($user['role'] === 'penghuni'): ?>
                        <li class="nav-item">
                            <a class="nav-link px-3 text-charcoal <?php echo ($activeNav === 'dashboard') ? 'fw-bold' : ''; ?>" href="<?php echo BASE_URL; ?>/dashboard/penghuni.php">
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 text-charcoal <?php echo ($activeNav === 'create_report') ? 'fw-bold' : ''; ?>" href="<?php echo BASE_URL; ?>/reports/create.php">
                                Buat Laporan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 text-charcoal <?php echo ($activeNav === 'my_reports') ? 'fw-bold' : ''; ?>" href="<?php echo BASE_URL; ?>/reports/index.php">
                                Laporan Saya
                            </a>
                        </li>
                    <?php elseif ($user['role'] === 'pengelola'): ?>
                        <li class="nav-item">
                            <a class="nav-link px-3 text-charcoal <?php echo ($activeNav === 'dashboard') ? 'fw-bold' : ''; ?>" href="<?php echo BASE_URL; ?>/dashboard/pengelola.php">
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 text-charcoal <?php echo ($activeNav === 'all_reports') ? 'fw-bold' : ''; ?>" href="<?php echo BASE_URL; ?>/reports/index.php">
                                Semua Laporan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 text-charcoal <?php echo ($activeNav === 'repair_history') ? 'fw-bold' : ''; ?>" href="<?php echo BASE_URL; ?>/repairs/history.php">
                                Riwayat Perbaikan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 text-charcoal <?php echo ($activeNav === 'theme_settings') ? 'fw-bold text-primary-purple' : ''; ?>" href="<?php echo BASE_URL; ?>/settings/tampilan.php">
                                Pengaturan Tampilan
                            </a>
                        </li>
                    <?php elseif ($user['role'] === 'teknisi'): ?>
                        <li class="nav-item">
                            <a class="nav-link px-3 text-charcoal <?php echo ($activeNav === 'dashboard') ? 'fw-bold' : ''; ?>" href="<?php echo BASE_URL; ?>/technician/dashboard.php">
                                Tugas Perbaikan
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- User Profile Dropdown -->
                    <li class="nav-item dropdown ms-lg-2">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 px-3 py-1.5 rounded-pill border bg-white text-charcoal shadow-xs" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="border-color: var(--color-pastel-lilac) !important;">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 28px; height: 28px; background-color: var(--color-light-lilac); color: var(--color-primary-purple); font-weight: bold; font-size: 0.85rem;">
                                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                            </span>
                            <span class="fw-semibold text-truncate" style="max-width: 140px; font-size: 0.9rem;"><?php echo e($user['name']); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-4 p-2 mt-2" style="background-color: #FFFFFF;">
                            <li class="px-3 py-2 border-bottom">
                                <small class="text-muted d-block">Masuk sebagai</small>
                                <span class="fw-bold text-charcoal"><?php echo e($user['username']); ?></span>
                                <?php if (!empty($user['room_number'])): ?>
                                    <small class="badge bg-light text-muted border mt-1 d-block w-fit"><?php echo e($user['room_number']); ?></small>
                                <?php endif; ?>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 rounded-3 mt-1" href="<?php echo BASE_URL; ?>/profile/index.php">
                                    <i class="bi bi-person me-2"></i> Profil & Sandi
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item py-2 rounded-3 text-danger" href="<?php echo BASE_URL; ?>/logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i> Keluar
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-outline-purple px-4 py-1.5 fs-7" href="<?php echo BASE_URL; ?>/login.php">
                            Masuk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary-purple px-4 py-1.5 fs-7" href="<?php echo BASE_URL; ?>/register.php">
                            Registrasi Penghuni
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
