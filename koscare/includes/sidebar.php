<?php
/**
 * KosCare - Sidebar Navigasi untuk Panel Dashboard
 */
defined('KOSCARE_APP') or define('KOSCARE_APP', true);

$user = currentUser();
$activeNav = $activeNav ?? 'dashboard';
?>
<aside class="sidebar-wrapper d-none d-lg-block">
    <div class="sidebar-card">
        <!-- Info Mini User -->
        <div class="sidebar-user-header p-3 mb-3 rounded-4">
            <div class="d-flex align-items-center gap-3">
                <div class="user-avatar-md">
                    <?php echo strtoupper(substr($user['name'] ?? 'U', 0, 1)); ?>
                </div>
                <div class="overflow-hidden">
                    <h6 class="mb-0 fw-bold text-truncate text-charcoal"><?php echo e($user['name']); ?></h6>
                    <span class="badge-role-tag"><?php echo ucfirst(e($user['role'])); ?></span>
                    <?php if (!empty($user['room_number'])): ?>
                        <small class="d-block text-taupe mt-0.5 text-truncate"><i class="bi bi-geo-alt me-1"></i><?php echo e($user['room_number']); ?></small>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Menu Navigasi Sesuai Role -->
        <nav class="sidebar-menu">
            <small class="sidebar-section-title">NAVIGASI UTAMA</small>
            <ul class="list-unstyled mb-4">
                <?php if ($user['role'] === 'penghuni'): ?>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/dashboard/penghuni.php" class="sidebar-link <?php echo ($activeNav === 'dashboard') ? 'active' : ''; ?>">
                            <i class="bi bi-grid-fill"></i>
                            <span>Ringkasan Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/reports/create.php" class="sidebar-link <?php echo ($activeNav === 'create_report') ? 'active' : ''; ?>">
                            <i class="bi bi-plus-circle-fill"></i>
                            <span>Buat Laporan Baru</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/reports/index.php" class="sidebar-link <?php echo ($activeNav === 'my_reports') ? 'active' : ''; ?>">
                            <i class="bi bi-card-checklist"></i>
                            <span>Daftar Laporan Saya</span>
                        </a>
                    </li>
                <?php elseif ($user['role'] === 'pengelola'): ?>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/dashboard/pengelola.php" class="sidebar-link <?php echo ($activeNav === 'dashboard') ? 'active' : ''; ?>">
                            <i class="bi bi-speedometer2"></i>
                            <span>Ringkasan & Metrik</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/reports/index.php" class="sidebar-link <?php echo ($activeNav === 'all_reports') ? 'active' : ''; ?>">
                            <i class="bi bi-inbox-fill"></i>
                            <span>Semua Laporan Kerusakan</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/repairs/history.php" class="sidebar-link <?php echo ($activeNav === 'repair_history') ? 'active' : ''; ?>">
                            <i class="bi bi-clock-history"></i>
                            <span>Riwayat Perbaikan Selesai</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/settings/tampilan.php" class="sidebar-link <?php echo ($activeNav === 'theme_settings') ? 'active' : ''; ?>">
                            <i class="bi bi-palette-fill"></i>
                            <span>Pengaturan Tampilan</span>
                        </a>
                    </li>
                <?php elseif ($user['role'] === 'teknisi'): ?>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/technician/dashboard.php" class="sidebar-link <?php echo ($activeNav === 'dashboard') ? 'active' : ''; ?>">
                            <i class="bi bi-tools"></i>
                            <span>Tugas Perbaikan Saya</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <small class="sidebar-section-title">PENGATURAN AKUN</small>
            <ul class="list-unstyled mb-0">
                <li>
                    <a href="<?php echo BASE_URL; ?>/profile/index.php" class="sidebar-link <?php echo ($activeNav === 'profile') ? 'active' : ''; ?>">
                        <i class="bi bi-person-gear"></i>
                        <span>Profil & Ubah Sandi</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/logout.php" class="sidebar-link text-danger">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Keluar Sesi</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Help Card Mini -->
        <div class="sidebar-support-box mt-4 p-3 rounded-4">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-shield-check text-sage fs-5"></i>
                <span class="fw-bold fs-7">KosCare Support</span>
            </div>
            <p class="text-taupe fs-8 mb-0">Butuh bantuan darurat? Hubungi nomor darurat pengelola kos.</p>
        </div>
    </div>
</aside>
