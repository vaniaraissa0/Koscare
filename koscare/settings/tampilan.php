<?php
/**
 * KosCare - Pengaturan Tampilan & Kustomisasi Identitas Website
 * Khusus untuk Pengelola KosCare
 */

define('KOSCARE_APP', true);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/theme.php';

// Hak Akses: Hanya akun dengan role pengelola
requireRole('pengelola');

$db = getDB();
$currentTheme = getThemeSettings();
$presets = getThemePresets();
$fonts = getSupportedFonts();

$errors = [];
$successMsg = '';

// Proses Simpan Pengaturan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCSRFToken($token)) {
        $errors[] = 'Validasi sesi keamanan (CSRF) kedaluwarsa. Silakan muat ulang halaman.';
    } else {
        $action = $_POST['action'] ?? 'save_theme';

        // 1. Kembalikan ke Tema Default
        if ($action === 'reset_default') {
            try {
                $stmt = $db->prepare("UPDATE `theme_settings` SET 
                    `preset_name` = 'Pastel Lilac',
                    `color_primary` = '#9B82D8',
                    `color_secondary` = '#DCCEF9',
                    `color_background` = '#F5F0FF',
                    `color_card` = '#FFFFFF',
                    `color_button` = '#9B82D8',
                    `color_text` = '#454052',
                    `font_family` = 'Times New Roman, Times, Georgia, serif',
                    `font_size_scale` = 'normal',
                    `app_name` = 'KosCare',
                    `app_tagline` = 'Perawatan Kos Jadi Lebih Mudah',
                    `logo_size` = 'medium',
                    `card_radius` = '1rem',
                    `button_shape` = 'pill'
                    WHERE `id` = 1");
                $stmt->execute();

                setFlash('success', 'Tema dan identitas tampilan berhasil dikembalikan ke pengaturan bawaan (Pastel Lilac & Times New Roman).');
                redirect(BASE_URL . '/settings/tampilan.php');
            } catch (PDOException $e) {
                $errors[] = 'Gagal mereset tema: ' . $e->getMessage();
            }
        }

        // 2. Hapus Logo Kustom
        elseif ($action === 'delete_logo') {
            try {
                if (!empty($currentTheme['logo_path'])) {
                    $fileToDelete = __DIR__ . '/../' . $currentTheme['logo_path'];
                    if (file_exists($fileToDelete)) {
                        @unlink($fileToDelete);
                    }
                }
                $stmt = $db->prepare("UPDATE `theme_settings` SET `logo_path` = NULL WHERE `id` = 1");
                $stmt->execute();
                setFlash('success', 'Logo kustom telah dihapus dan diganti dengan logo ikon standar.');
                redirect(BASE_URL . '/settings/tampilan.php');
            } catch (PDOException $e) {
                $errors[] = 'Gagal menghapus logo: ' . $e->getMessage();
            }
        }

        // 3. Simpan Kustomisasi Tema
        elseif ($action === 'save_theme') {
            $presetName = trim($_POST['preset_name'] ?? 'Custom');
            $colorPrimary = trim($_POST['color_primary'] ?? '#9B82D8');
            $colorSecondary = trim($_POST['color_secondary'] ?? '#DCCEF9');
            $colorBg = trim($_POST['color_background'] ?? '#F5F0FF');
            $colorCard = trim($_POST['color_card'] ?? '#FFFFFF');
            $colorButton = trim($_POST['color_button'] ?? '#9B82D8');
            $colorText = trim($_POST['color_text'] ?? '#454052');
            $fontFamily = trim($_POST['font_family'] ?? 'Times New Roman, Times, Georgia, serif');
            $fontSizeScale = trim($_POST['font_size_scale'] ?? 'normal');
            $appName = trim($_POST['app_name'] ?? 'KosCare');
            $appTagline = trim($_POST['app_tagline'] ?? 'Perawatan Kos Jadi Lebih Mudah');
            $logoSize = trim($_POST['logo_size'] ?? 'medium');
            $cardRadius = trim($_POST['card_radius'] ?? '1rem');
            $buttonShape = trim($_POST['button_shape'] ?? 'pill');

            // Validasi Format Warna Hex
            $hexRegex = '/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/';
            if (!preg_match($hexRegex, $colorPrimary) || !preg_match($hexRegex, $colorSecondary) ||
                !preg_match($hexRegex, $colorBg) || !preg_match($hexRegex, $colorCard) ||
                !preg_match($hexRegex, $colorButton) || !preg_match($hexRegex, $colorText)) {
                $errors[] = 'Format warna tidak valid. Gunakan format kode heksadesimal (contoh: #9B82D8).';
            }

            // Validasi Identitas
            if (empty($appName)) {
                $errors[] = 'Nama website tidak boleh kosong.';
            }

            $uploadedLogoPath = $currentTheme['logo_path'] ?? null;

            // Validasi & Unggah File Logo jika ada
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
                    $errors[] = 'Terjadi kesalahan saat mengunggah file logo.';
                } else {
                    $fileTmp = $_FILES['logo']['tmp_name'];
                    $fileSize = $_FILES['logo']['size'];
                    $fileName = $_FILES['logo']['name'];

                    // Maks 2MB
                    if ($fileSize > 2 * 1024 * 1024) {
                        $errors[] = 'Ukuran file logo maksimal adalah 2MB.';
                    }

                    // Cek MIME type dengan finfo
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_file($finfo, $fileTmp);
                    finfo_close($finfo);

                    $allowedMimes = [
                        'image/png' => 'png',
                        'image/jpeg' => 'jpg',
                        'image/webp' => 'webp',
                    ];

                    if (!array_key_exists($mime, $allowedMimes)) {
                        $errors[] = 'Format file logo hanya mendukung PNG, JPG, JPEG, atau WebP.';
                    } else {
                        $ext = $allowedMimes[$mime];
                        $uploadDir = __DIR__ . '/../uploads/logos/';
                        if (!is_dir($uploadDir)) {
                            @mkdir($uploadDir, 0755, true);
                        }

                        $newFileName = 'logo_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                        $targetPath = $uploadDir . $newFileName;

                        if (move_uploaded_file($fileTmp, $targetPath)) {
                            // Hapus logo lama jika ada
                            if (!empty($currentTheme['logo_path'])) {
                                $oldFile = __DIR__ . '/../' . $currentTheme['logo_path'];
                                if (file_exists($oldFile)) {
                                    @unlink($oldFile);
                                }
                            }
                            $uploadedLogoPath = 'uploads/logos/' . $newFileName;
                        } else {
                            $errors[] = 'Gagal menyimpan file logo yang diunggah.';
                        }
                    }
                }
            }

            if (empty($errors)) {
                try {
                    $stmt = $db->prepare("INSERT INTO `theme_settings` (
                        `id`, `preset_name`, `color_primary`, `color_secondary`, `color_background`,
                        `color_card`, `color_button`, `color_text`, `font_family`, `font_size_scale`,
                        `app_name`, `app_tagline`, `logo_path`, `logo_size`, `card_radius`, `button_shape`
                    ) VALUES (
                        1, :preset, :primary, :secondary, :bg, :card, :btn, :text, :font, :fscale,
                        :appname, :tagline, :logo, :logosize, :radius, :btnshape
                    ) ON DUPLICATE KEY UPDATE
                        `preset_name` = VALUES(`preset_name`),
                        `color_primary` = VALUES(`color_primary`),
                        `color_secondary` = VALUES(`color_secondary`),
                        `color_background` = VALUES(`color_background`),
                        `color_card` = VALUES(`color_card`),
                        `color_button` = VALUES(`color_button`),
                        `color_text` = VALUES(`color_text`),
                        `font_family` = VALUES(`font_family`),
                        `font_size_scale` = VALUES(`font_size_scale`),
                        `app_name` = VALUES(`app_name`),
                        `app_tagline` = VALUES(`app_tagline`),
                        `logo_path` = VALUES(`logo_path`),
                        `logo_size` = VALUES(`logo_size`),
                        `card_radius` = VALUES(`card_radius`),
                        `button_shape` = VALUES(`button_shape`)");

                    $stmt->execute([
                        ':preset' => $presetName,
                        ':primary' => $colorPrimary,
                        ':secondary' => $colorSecondary,
                        ':bg' => $colorBg,
                        ':card' => $colorCard,
                        ':btn' => $colorButton,
                        ':text' => $colorText,
                        ':font' => $fontFamily,
                        ':fscale' => $fontSizeScale,
                        ':appname' => $appName,
                        ':tagline' => $appTagline,
                        ':logo' => $uploadedLogoPath,
                        ':logosize' => $logoSize,
                        ':radius' => $cardRadius,
                        ':btnshape' => $buttonShape,
                    ]);

                    setFlash('success', 'Pengaturan tema dan identitas website berhasil disimpan secara permanen.');
                    redirect(BASE_URL . '/settings/tampilan.php');
                } catch (PDOException $e) {
                    $errors[] = 'Gagal menyimpan perubahan ke database: ' . $e->getMessage();
                }
            }
        }
    }
}

// Refresh settings setelah query
$currentTheme = getThemeSettings();
$pageTitle = 'Pengaturan Tampilan & Kustomisasi — ' . getAppName();
$activeNav = 'theme_settings';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container-fluid px-lg-4 py-4 flex-grow-1">
    <div class="row">
        <!-- Sidebar Desktop -->
        <div class="col-lg-3 col-xl-2 mb-4">
            <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
        </div>

        <!-- Konten Utama Pengaturan -->
        <div class="col-lg-9 col-xl-10">
            <!-- Breadcrumb & Header Halaman -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Pengaturan Tampilan</h3>
                    <p class="text-taupe mb-0 small">Kustomisasi palet warna, tipografi, identitas merek, dan bentuk tampilan website.</p>
                </div>
                <div class="d-flex gap-2">
                    <form method="POST" action="" onsubmit="return confirm('Kembalikan semua pengaturan tampilan ke setelan bawaan Pastel Lilac & Times New Roman?');">
                        <?php echo csrfField(); ?>
                        <input type="hidden" name="action" value="reset_default">
                        <button type="submit" class="btn btn-outline-purple px-3 py-2 text-xs">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Kembalikan ke Default
                        </button>
                    </form>
                </div>
            </div>

            <!-- Pesan Notifikasi -->
            <?php if (hasFlash('success')): ?>
                <div class="alert alert-success border-0 rounded-4 shadow-xs py-3 px-4 mb-4 d-flex align-items-center justify-content-between" style="background-color: #EBF8EE; color: #235B2B;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <span><?php echo e(getFlash('success')); ?></span>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger border-0 rounded-4 shadow-xs py-3 px-4 mb-4" style="background-color: #FCE8E6; color: #8A332A;">
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $err): ?>
                            <li><?php echo e($err); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Formulir Kustomisasi -->
            <form method="POST" action="" enctype="multipart/form-data" id="themeForm">
                <?php echo csrfField(); ?>
                <input type="hidden" name="action" value="save_theme">
                <input type="hidden" name="preset_name" id="inputPresetName" value="<?php echo e($currentTheme['preset_name'] ?? 'Pastel Lilac'); ?>">

                <div class="row g-4">
                    <!-- Kolom Kiri: Kontrol Pengaturan -->
                    <div class="col-xl-7">
                        <!-- BAGIAN A: PALET WARNA & PRESET -->
                        <div class="card card-aesthetic p-4 mb-4 shadow-sm border-0">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 32px; height: 32px; background: var(--color-light-lilac); color: var(--color-primary-purple);">
                                    <i class="bi bi-palette"></i>
                                </span>
                                <h5 class="fw-bold mb-0">Tema Warna Siap Pakai</h5>
                            </div>
                            <p class="text-taupe small mb-3">Pilih kombinasi palet pastel yang telah diselaraskan atau atur warna khusus dengan color picker.</p>

                            <!-- Pilihan Preset -->
                            <div class="row g-2 mb-4">
                                <?php foreach ($presets as $pName => $pColors): ?>
                                    <div class="col-sm-6 col-md-4">
                                        <button type="button" 
                                            class="preset-card-btn w-100 p-2.5 rounded-3 border text-start transition <?php echo (($currentTheme['preset_name'] ?? '') === $pName) ? 'active-preset' : ''; ?>"
                                            onclick="applyPreset('<?php echo $pName; ?>', <?php echo htmlspecialchars(json_encode($pColors)); ?>)"
                                            style="background-color: #FFFFFF;">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="fw-bold small text-charcoal"><?php echo $pName; ?></span>
                                                <?php if (($currentTheme['preset_name'] ?? '') === $pName): ?>
                                                    <i class="bi bi-check-circle-fill text-primary-purple"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="d-flex gap-1">
                                                <span class="rounded-circle" style="width: 16px; height: 16px; background-color: <?php echo $pColors['primary']; ?>;" title="Utama"></span>
                                                <span class="rounded-circle" style="width: 16px; height: 16px; background-color: <?php echo $pColors['secondary']; ?>;" title="Sekunder"></span>
                                                <span class="rounded-circle" style="width: 16px; height: 16px; background-color: <?php echo $pColors['background']; ?>;" title="Latar"></span>
                                                <span class="rounded-circle" style="width: 16px; height: 16px; background-color: <?php echo $pColors['button']; ?>;" title="Tombol"></span>
                                            </div>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <hr class="my-3 opacity-25">

                            <h6 class="fw-bold small text-charcoal mb-3">Penyesuaian Detail Warna (Color Picker)</h6>
                            <div class="row g-3">
                                <!-- Warna Tombol & Utama -->
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-charcoal mb-1">Warna Tombol Utama</label>
                                    <div class="input-group input-group-sm">
                                        <input type="color" class="form-control form-control-color" id="picker_button" name="color_button" value="<?php echo e($currentTheme['color_button'] ?? '#9B82D8'); ?>" onchange="syncColorInput('button', this.value)">
                                        <input type="text" class="form-control" id="hex_button" value="<?php echo e($currentTheme['color_button'] ?? '#9B82D8'); ?>" oninput="syncColorPicker('button', this.value)">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-charcoal mb-1">Warna Utama (Aksen Primer)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="color" class="form-control form-control-color" id="picker_primary" name="color_primary" value="<?php echo e($currentTheme['color_primary'] ?? '#9B82D8'); ?>" onchange="syncColorInput('primary', this.value)">
                                        <input type="text" class="form-control" id="hex_primary" value="<?php echo e($currentTheme['color_primary'] ?? '#9B82D8'); ?>" oninput="syncColorPicker('primary', this.value)">
                                    </div>
                                </div>
                                <!-- Warna Sekunder & Latar Belakang -->
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-charcoal mb-1">Warna Sekunder / Badge</label>
                                    <div class="input-group input-group-sm">
                                        <input type="color" class="form-control form-control-color" id="picker_secondary" name="color_secondary" value="<?php echo e($currentTheme['color_secondary'] ?? '#DCCEF9'); ?>" onchange="syncColorInput('secondary', this.value)">
                                        <input type="text" class="form-control" id="hex_secondary" value="<?php echo e($currentTheme['color_secondary'] ?? '#DCCEF9'); ?>" oninput="syncColorPicker('secondary', this.value)">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-charcoal mb-1">Warna Latar Belakang Halaman</label>
                                    <div class="input-group input-group-sm">
                                        <input type="color" class="form-control form-control-color" id="picker_background" name="color_background" value="<?php echo e($currentTheme['color_background'] ?? '#F5F0FF'); ?>" onchange="syncColorInput('background', this.value)">
                                        <input type="text" class="form-control" id="hex_background" value="<?php echo e($currentTheme['color_background'] ?? '#F5F0FF'); ?>" oninput="syncColorPicker('background', this.value)">
                                    </div>
                                </div>
                                <!-- Warna Kartu & Teks -->
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-charcoal mb-1">Warna Kartu Konten</label>
                                    <div class="input-group input-group-sm">
                                        <input type="color" class="form-control form-control-color" id="picker_card" name="color_card" value="<?php echo e($currentTheme['color_card'] ?? '#FFFFFF'); ?>" onchange="syncColorInput('card', this.value)">
                                        <input type="text" class="form-control" id="hex_card" value="<?php echo e($currentTheme['color_card'] ?? '#FFFFFF'); ?>" oninput="syncColorPicker('card', this.value)">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-charcoal mb-1">Warna Teks Utama</label>
                                    <div class="input-group input-group-sm">
                                        <input type="color" class="form-control form-control-color" id="picker_text" name="color_text" value="<?php echo e($currentTheme['color_text'] ?? '#454052'); ?>" onchange="syncColorInput('text', this.value)">
                                        <input type="text" class="form-control" id="hex_text" value="<?php echo e($currentTheme['color_text'] ?? '#454052'); ?>" oninput="syncColorPicker('text', this.value)">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- BAGIAN B: TIPOGRAFI & FONT -->
                        <div class="card card-aesthetic p-4 mb-4 shadow-sm border-0">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 32px; height: 32px; background: var(--color-light-lilac); color: var(--color-primary-purple);">
                                    <i class="bi bi-type"></i>
                                </span>
                                <h5 class="fw-bold mb-0">Pilihan Font & Tipografi Global</h5>
                            </div>
                            <p class="small text-taupe mb-3">
                                Pilih font utama website. Perubahan font akan diterapkan secara serentak ke seluruh halaman: landing page, dashboard penghuni, dashboard pengelola, formulir, tabel, tombol, dan navigasi.
                            </p>

                            <!-- Pilihan Cepat Kartu Font -->
                            <label class="form-label small fw-bold text-charcoal mb-2">Pilih Jenis Font:</label>
                            <div class="row g-2 mb-3">
                                <?php foreach ($fonts as $fontVal => $fontLabel): 
                                    $isSelected = (($currentTheme['font_family'] ?? '') === $fontVal);
                                ?>
                                    <div class="col-sm-6">
                                        <div class="p-2.5 rounded-3 border font-card-choice <?php echo $isSelected ? 'border-primary-purple bg-light-lilac' : 'bg-white'; ?>" 
                                             style="cursor: pointer; font-family: <?php echo htmlspecialchars($fontVal); ?>;" 
                                             onclick="selectFontChoice('<?php echo htmlspecialchars(addslashes($fontVal)); ?>', this)">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold small" style="font-family: <?php echo htmlspecialchars($fontVal); ?>;">
                                                    <?php echo explode('(', $fontLabel)[0]; ?>
                                                </span>
                                                <i class="bi bi-check-circle-fill font-check-icon text-primary-purple <?php echo $isSelected ? '' : 'd-none'; ?>"></i>
                                            </div>
                                            <div class="text-taupe small mt-1" style="font-size: 0.78rem; font-family: <?php echo htmlspecialchars($fontVal); ?>;">
                                                Aa Bb Gg 123 - KosCare Aesthetic
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-charcoal mb-1">Dropdown Font</label>
                                <select class="form-select form-select-sm" name="font_family" id="inputFontFamily" onchange="syncFontSelect(this.value)">
                                    <?php foreach ($fonts as $fontVal => $fontLabel): ?>
                                        <option value="<?php echo htmlspecialchars($fontVal); ?>" <?php echo (($currentTheme['font_family'] ?? '') === $fontVal) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($fontLabel); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- KOTAK CONTOH TULISAN LANGSUNG (LIVE FONT PREVIEW) -->
                            <div class="p-3 rounded-3 border mb-3" style="background-color: var(--color-pure-white); border-color: var(--color-pastel-lilac) !important;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="small fw-bold text-charcoal">Contoh Tulisan Langsung (Live Preview)</span>
                                    <span class="badge badge-pastel" id="activeFontBadge" style="background: var(--color-light-lilac); color: var(--color-primary-purple);">
                                        Times New Roman
                                    </span>
                                </div>
                                <div id="liveFontSampleBox" class="p-3 rounded-2 border bg-light" style="font-family: <?php echo htmlspecialchars($currentTheme['font_family'] ?? 'Times New Roman, Times, Georgia, serif'); ?>;">
                                    <h5 class="fw-bold mb-1" id="sampleFontHeading">Perawatan Fasilitas Kos Jadi Lebih Mudah</h5>
                                    <p class="small text-charcoal mb-2" id="sampleFontParagraph">
                                        Kamar 102 — Kran wastafel air menetes terus menerus sejak pagi. Laporan telah diverifikasi oleh pengelola dan teknisi siap menangani perbaikan.
                                    </p>
                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        <span class="badge badge-pastel badge-status-diproses">Sedang Diproses</span>
                                        <span class="badge badge-pastel badge-status-selesai">Selesai: Rp 125.000</span>
                                        <small class="text-taupe">Nomor Tiket: <strong>KC-202610-0001</strong></small>
                                    </div>
                                    <div class="mt-2 pt-2 border-top">
                                        <label class="form-label small text-taupe mb-1" style="font-size: 0.75rem;">Uji Ketik Teks Anda Sendiri:</label>
                                        <input type="text" class="form-control form-control-sm" id="customFontTester" placeholder="Ketik kata atau kalimat apa saja untuk melihat tampilan font..." oninput="updateCustomFontText(this.value)">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-bold text-charcoal mb-1">Skala Ukuran Teks</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="font_size_scale" id="scale_compact" value="compact" <?php echo (($currentTheme['font_size_scale'] ?? '') === 'compact') ? 'checked' : ''; ?> onchange="updateLivePreview()">
                                        <label class="form-check-label small" for="scale_compact">Ringkas (95%)</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="font_size_scale" id="scale_normal" value="normal" <?php echo (($currentTheme['font_size_scale'] ?? 'normal') === 'normal') ? 'checked' : ''; ?> onchange="updateLivePreview()">
                                        <label class="form-check-label small" for="scale_normal">Normal (100% - Bawaan)</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="font_size_scale" id="scale_large" value="large" <?php echo (($currentTheme['font_size_scale'] ?? '') === 'large') ? 'checked' : ''; ?> onchange="updateLivePreview()">
                                        <label class="form-check-label small" for="scale_large">Nyaman (105%)</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- BAGIAN C: IDENTITAS WEBSITE & LOGO -->
                        <div class="card card-aesthetic p-4 mb-4 shadow-sm border-0">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 32px; height: 32px; background: var(--color-light-lilac); color: var(--color-primary-purple);">
                                    <i class="bi bi-shield-check"></i>
                                </span>
                                <h5 class="fw-bold mb-0">Identitas & Logo Website</h5>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-charcoal mb-1">Nama Aplikasi / Kos</label>
                                    <input type="text" class="form-control form-control-sm" name="app_name" id="inputAppName" value="<?php echo e($currentTheme['app_name'] ?? 'KosCare'); ?>" oninput="updateLivePreview()" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-charcoal mb-1">Tagline Singkat</label>
                                    <input type="text" class="form-control form-control-sm" name="app_tagline" id="inputAppTagline" value="<?php echo e($currentTheme['app_tagline'] ?? 'Perawatan Kos Jadi Lebih Mudah'); ?>" oninput="updateLivePreview()" required>
                                </div>
                            </div>

                            <!-- Unggah Logo -->
                            <div class="p-3 rounded-3 border bg-light mb-3">
                                <label class="form-label small fw-bold text-charcoal mb-1">Unggah Logo Kustom</label>
                                <input type="file" class="form-control form-control-sm mb-2" name="logo" id="inputLogoFile" accept=".png, .jpg, .jpeg, .webp" onchange="previewLogoFile(this)">
                                <small class="text-taupe d-block">Format: PNG, JPG, atau WebP transparan (Maks. 2MB). Biarkan kosong jika tidak ingin mengubah logo saat ini.</small>

                                <div class="d-flex align-items-center gap-3 mt-3">
                                    <div class="logo-preview-box p-2 rounded border bg-white text-center" style="min-width: 90px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                        <?php if (!empty($currentTheme['logo_path'])): ?>
                                            <img id="logoPreviewImg" src="<?php echo BASE_URL . '/' . htmlspecialchars($currentTheme['logo_path']); ?>" alt="Logo" style="max-height: 36px; max-width: 100%;">
                                        <?php else: ?>
                                            <span id="logoPlaceholder" class="text-primary-purple fs-4"><i class="bi bi-house-heart"></i></span>
                                            <img id="logoPreviewImg" src="" alt="Logo" style="max-height: 36px; max-width: 100%; display: none;">
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="d-flex gap-2 mb-1">
                                            <label class="small text-charcoal fw-semibold">Ukuran Tampilan:</label>
                                            <div class="form-check form-check-inline me-2">
                                                <input class="form-check-input" type="radio" name="logo_size" id="size_sm" value="small" <?php echo (($currentTheme['logo_size'] ?? '') === 'small') ? 'checked' : ''; ?> onchange="updateLivePreview()">
                                                <label class="form-check-label small" for="size_sm">Kecil</label>
                                            </div>
                                            <div class="form-check form-check-inline me-2">
                                                <input class="form-check-input" type="radio" name="logo_size" id="size_md" value="medium" <?php echo (($currentTheme['logo_size'] ?? 'medium') === 'medium') ? 'checked' : ''; ?> onchange="updateLivePreview()">
                                                <label class="form-check-label small" for="size_md">Sedang</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="logo_size" id="size_lg" value="large" <?php echo (($currentTheme['logo_size'] ?? '') === 'large') ? 'checked' : ''; ?> onchange="updateLivePreview()">
                                                <label class="form-check-label small" for="size_lg">Besar</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- BAGIAN D: BENTUK KOMPONEN & KARTU -->
                        <div class="card card-aesthetic p-4 mb-4 shadow-sm border-0">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 32px; height: 32px; background: var(--color-light-lilac); color: var(--color-primary-purple);">
                                    <i class="bi bi-bounding-box"></i>
                                </span>
                                <h5 class="fw-bold mb-0">Bentuk Komponen & Kartu</h5>
                            </div>

                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-charcoal mb-1">Radius Sudut Kartu</label>
                                    <select class="form-select form-select-sm" name="card_radius" id="inputCardRadius" onchange="updateLivePreview()">
                                        <option value="0.5rem" <?php echo (($currentTheme['card_radius'] ?? '') === '0.5rem') ? 'selected' : ''; ?>>Kotak Halus (8px)</option>
                                        <option value="1rem" <?php echo (($currentTheme['card_radius'] ?? '1rem') === '1rem') ? 'selected' : ''; ?>>Lembut / Standar (16px - Bawaan)</option>
                                        <option value="1.5rem" <?php echo (($currentTheme['card_radius'] ?? '') === '1.5rem') ? 'selected' : ''; ?>>Ekstra Bulat (24px)</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label small fw-bold text-charcoal mb-1">Bentuk Tombol</label>
                                    <select class="form-select form-select-sm" name="button_shape" id="inputButtonShape" onchange="updateLivePreview()">
                                        <option value="pill" <?php echo (($currentTheme['button_shape'] ?? 'pill') === 'pill') ? 'selected' : ''; ?>>Kapsul / Membulat Penuh (Bawaan)</option>
                                        <option value="rounded" <?php echo (($currentTheme['button_shape'] ?? '') === 'rounded') ? 'selected' : ''; ?>>Membulat Sedang (10px)</option>
                                        <option value="square" <?php echo (($currentTheme['button_shape'] ?? '') === 'square') ? 'selected' : ''; ?>>Kotak Halus (4px)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi Simpan & Batal -->
                        <div class="d-flex flex-wrap gap-2 mb-5">
                            <button type="submit" class="btn btn-primary-purple px-4 py-2.5 fw-bold shadow-xs">
                                <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan
                            </button>
                            <a href="<?php echo BASE_URL; ?>/settings/tampilan.php" class="btn btn-outline-secondary px-4 py-2.5">
                                Batalkan
                            </a>
                        </div>
                    </div>

                    <!-- Kolom Kanan: PRATINJAU LANGSUNG (LIVE PREVIEW) -->
                    <div class="col-xl-5">
                        <div class="sticky-top" style="top: 80px; z-index: 10;">
                            <div class="card p-3 p-md-4 shadow-sm border" id="previewContainer" style="background-color: <?php echo e($currentTheme['color_background'] ?? '#F5F0FF'); ?>; border-color: rgba(220, 206, 249, 0.6) !important;">
                                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <i class="bi bi-eye-fill text-primary-purple"></i>
                                        <span class="fw-bold small text-charcoal">Pratinjau Langsung</span>
                                    </div>
                                    <span class="badge bg-white text-charcoal border small" id="previewPresetTag">
                                        <?php echo e($currentTheme['preset_name'] ?? 'Pastel Lilac'); ?>
                                    </span>
                                </div>

                                <!-- Simulasi Header / Navbar Mini -->
                                <div class="p-2.5 rounded-3 bg-white border mb-3 d-flex align-items-center justify-content-between shadow-xs">
                                    <div class="d-flex align-items-center gap-2">
                                        <span id="previewNavLogoIcon" class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 28px; height: 28px; background-color: <?php echo e($currentTheme['color_secondary'] ?? '#DCCEF9'); ?>; color: <?php echo e($currentTheme['color_button'] ?? '#9B82D8'); ?>;">
                                            <i class="bi bi-house-heart"></i>
                                        </span>
                                        <img id="previewNavLogoImg" src="<?php echo !empty($currentTheme['logo_path']) ? BASE_URL . '/' . htmlspecialchars($currentTheme['logo_path']) : ''; ?>" alt="Logo" style="height: 24px; max-width: 80px; object-fit: contain; <?php echo empty($currentTheme['logo_path']) ? 'display: none;' : ''; ?>">
                                        <span class="fw-bold small" id="previewNavTitle" style="color: <?php echo e($currentTheme['color_text'] ?? '#454052'); ?>;">
                                            <?php echo e($currentTheme['app_name'] ?? 'KosCare'); ?>
                                        </span>
                                    </div>
                                    <span class="badge rounded-pill px-2.5 py-1 text-xs" id="previewNavBadge" style="background-color: <?php echo e($currentTheme['color_secondary'] ?? '#DCCEF9'); ?>; color: <?php echo e($currentTheme['color_text'] ?? '#454052'); ?>;">
                                        Pengelola
                                    </span>
                                </div>

                                <!-- Simulasi Kartu Laporan Contoh -->
                                <div class="p-3.5 mb-3 border shadow-xs" id="previewCardSample" style="background-color: <?php echo e($currentTheme['color_card'] ?? '#FFFFFF'); ?>; border-radius: <?php echo e($currentTheme['card_radius'] ?? '1rem'); ?>;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge px-2 py-0.5 rounded-pill text-xs" id="previewUrgencyBadge" style="background-color: <?php echo e($currentTheme['color_secondary'] ?? '#DCCEF9'); ?>; color: <?php echo e($currentTheme['color_text'] ?? '#454052'); ?>;">
                                            Tinggi
                                        </span>
                                        <small class="text-muted" style="font-size: 0.75rem;">KC-202610-0001</small>
                                    </div>
                                    <h6 class="fw-bold mb-1" id="previewCardTitle" style="color: <?php echo e($currentTheme['color_text'] ?? '#454052'); ?>;">
                                        Kran Air Kamar Mandi Bocor
                                    </h6>
                                    <p class="mb-3 small" id="previewCardDesc" style="color: <?php echo e($currentTheme['color_text'] ?? '#454052'); ?>; opacity: 0.85; font-size: 0.82rem;">
                                        Kamar 102 &bull; Pelapor: Dimas Anggoro
                                    </p>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm px-3 text-white fw-bold" id="previewBtnSample" style="background-color: <?php echo e($currentTheme['color_button'] ?? '#9B82D8'); ?>; border-radius: 9999px;">
                                            Periksa Laporan
                                        </button>
                                        <button type="button" class="btn btn-sm px-2.5 border" id="previewBtnSecondary" style="border-color: <?php echo e($currentTheme['color_secondary'] ?? '#DCCEF9'); ?>; color: <?php echo e($currentTheme['color_text'] ?? '#454052'); ?>; border-radius: 9999px;">
                                            Detail
                                        </button>
                                    </div>
                                </div>

                                <!-- Simulasi Tagline / Footer -->
                                <div class="text-center p-2">
                                    <p class="mb-0 small fst-italic" id="previewTaglineText" style="color: <?php echo e($currentTheme['color_text'] ?? '#454052'); ?>; font-size: 0.85rem;">
                                        “<?php echo e($currentTheme['app_tagline'] ?? 'Perawatan Kos Jadi Lebih Mudah'); ?>”
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Fungsi Sinkronisasi Input Color & Hex
function syncColorInput(field, hex) {
    document.getElementById('hex_' + field).value = hex.toUpperCase();
    document.getElementById('inputPresetName').value = 'Custom';
    document.getElementById('previewPresetTag').textContent = 'Custom';
    updateLivePreview();
}

function syncColorPicker(field, hex) {
    if (/^#[0-9A-Fa-f]{6}$/.test(hex)) {
        document.getElementById('picker_' + field).value = hex;
        document.getElementById('inputPresetName').value = 'Custom';
        document.getElementById('previewPresetTag').textContent = 'Custom';
        updateLivePreview();
    }
}

// Terapkan Palet Preset
function applyPreset(name, colors) {
    document.getElementById('inputPresetName').value = name;
    document.getElementById('previewPresetTag').textContent = name;

    // Set nilai color inputs
    const map = {
        'button': colors.button,
        'primary': colors.primary,
        'secondary': colors.secondary,
        'background': colors.background,
        'card': colors.card,
        'text': colors.text
    };

    for (const [k, v] of Object.entries(map)) {
        if (document.getElementById('picker_' + k)) {
            document.getElementById('picker_' + k).value = v;
            document.getElementById('hex_' + k).value = v.toUpperCase();
        }
    }

    // Toggle active border pada tombol preset
    document.querySelectorAll('.preset-card-btn').forEach(btn => btn.classList.remove('active-preset'));
    event.currentTarget.classList.add('active-preset');

    updateLivePreview();
}

// Live Preview Updater
function updateLivePreview() {
    const btnColor = document.getElementById('picker_button').value;
    const secColor = document.getElementById('picker_secondary').value;
    const bgColor = document.getElementById('picker_background').value;
    const cardColor = document.getElementById('picker_card').value;
    const textColor = document.getElementById('picker_text').value;

    const fontVal = document.getElementById('inputFontFamily').value;
    const appName = document.getElementById('inputAppName').value || 'KosCare';
    const appTagline = document.getElementById('inputAppTagline').value || '';
    const cardRadius = document.getElementById('inputCardRadius').value;
    const buttonShape = document.getElementById('inputButtonShape').value;

    let btnRadius = '9999px';
    if (buttonShape === 'rounded') btnRadius = '10px';
    if (buttonShape === 'square') btnRadius = '4px';

    // Update Preview Container
    const container = document.getElementById('previewContainer');
    container.style.backgroundColor = bgColor;
    container.style.fontFamily = fontVal;

    // Update Elements
    document.getElementById('previewNavLogoIcon').style.backgroundColor = secColor;
    document.getElementById('previewNavLogoIcon').style.color = btnColor;
    document.getElementById('previewNavTitle').textContent = appName;
    document.getElementById('previewNavTitle').style.color = textColor;
    document.getElementById('previewNavBadge').style.backgroundColor = secColor;
    document.getElementById('previewNavBadge').style.color = textColor;

    // Update Card
    const card = document.getElementById('previewCardSample');
    card.style.backgroundColor = cardColor;
    card.style.borderRadius = cardRadius;
    document.getElementById('previewCardTitle').style.color = textColor;
    document.getElementById('previewCardDesc').style.color = textColor;
    document.getElementById('previewUrgencyBadge').style.backgroundColor = secColor;
    document.getElementById('previewUrgencyBadge').style.color = textColor;

    // Update Buttons
    const btn = document.getElementById('previewBtnSample');
    btn.style.backgroundColor = btnColor;
    btn.style.borderRadius = btnRadius;

    const btnSec = document.getElementById('previewBtnSecondary');
    btnSec.style.borderColor = secColor;
    btnSec.style.color = textColor;
    btnSec.style.borderRadius = btnRadius;

    // Update Tagline
    document.getElementById('previewTaglineText').textContent = '“' + appTagline + '”';
    document.getElementById('previewTaglineText').style.color = textColor;
}

// Fungsi Pilihan Font Interaktif & Live Preview Font
function selectFontChoice(fontVal, cardEl) {
    document.getElementById('inputFontFamily').value = fontVal;
    
    // Update active state visual pada kartu font
    document.querySelectorAll('.font-card-choice').forEach(c => {
        c.classList.remove('border-primary-purple', 'bg-light-lilac');
        c.classList.add('bg-white');
        const icon = c.querySelector('.font-check-icon');
        if (icon) icon.classList.add('d-none');
    });

    if (cardEl) {
        cardEl.classList.add('border-primary-purple', 'bg-light-lilac');
        cardEl.classList.remove('bg-white');
        const icon = cardEl.querySelector('.font-check-icon');
        if (icon) icon.classList.remove('d-none');
    }

    syncFontPreviewBox(fontVal);
    updateLivePreview();
}

function syncFontSelect(fontVal) {
    document.querySelectorAll('.font-card-choice').forEach(c => {
        const styleFont = c.getAttribute('style') || '';
        const isMatch = styleFont.includes(fontVal.split(',')[0]);
        if (isMatch) {
            c.classList.add('border-primary-purple', 'bg-light-lilac');
            c.classList.remove('bg-white');
            const icon = c.querySelector('.font-check-icon');
            if (icon) icon.classList.remove('d-none');
        } else {
            c.classList.remove('border-primary-purple', 'bg-light-lilac');
            c.classList.add('bg-white');
            const icon = c.querySelector('.font-check-icon');
            if (icon) icon.classList.add('d-none');
        }
    });

    syncFontPreviewBox(fontVal);
    updateLivePreview();
}

function syncFontPreviewBox(fontVal) {
    const box = document.getElementById('liveFontSampleBox');
    if (box) {
        box.style.fontFamily = fontVal;
    }
    const badge = document.getElementById('activeFontBadge');
    if (badge) {
        badge.textContent = fontVal.split(',')[0].replace(/'/g, '').trim();
    }
}

function updateCustomFontText(text) {
    const p = document.getElementById('sampleFontParagraph');
    if (p) {
        if (text && text.trim().length > 0) {
            p.textContent = text;
        } else {
            p.textContent = 'Kamar 102 — Kran wastafel air menetes terus menerus sejak pagi. Laporan telah diverifikasi oleh pengelola dan teknisi siap menangani perbaikan.';
        }
    }
}

// Preview Logo Unggahan
function previewLogoFile(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewImg = document.getElementById('logoPreviewImg');
            const placeholder = document.getElementById('logoPlaceholder');
            previewImg.src = e.target.result;
            previewImg.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';

            // Di navbar preview
            const navImg = document.getElementById('previewNavLogoImg');
            navImg.src = e.target.result;
            navImg.style.display = 'inline-block';
            document.getElementById('previewNavLogoIcon').style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<style>
.preset-card-btn {
    border-color: rgba(220, 206, 249, 0.7) !important;
    cursor: pointer;
}
.preset-card-btn:hover {
    border-color: var(--color-primary-purple) !important;
    background-color: var(--color-light-lilac) !important;
}
.active-preset {
    border-color: var(--color-primary-purple) !important;
    background-color: var(--color-light-lilac) !important;
    box-shadow: 0 0 0 2px rgba(155, 130, 216, 0.25);
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
