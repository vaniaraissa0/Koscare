<?php
/**
 * KosCare - Helper Pengaturan Tampilan & Kustomisasi Identitas Website
 * Mengelola tema dinamis (warna, font, logo, tagline, gaya kartu) dari database MySQL
 */

defined('KOSCARE_APP') or define('KOSCARE_APP', true);

require_once __DIR__ . '/../config/database.php';

/**
 * Daftar tema siap pakai (Presets)
 */
function getThemePresets(): array {
    return [
        'Pastel Lilac' => [
            'primary' => '#9B82D8',
            'secondary' => '#DCCEF9',
            'background' => '#F5F0FF',
            'card' => '#FFFFFF',
            'button' => '#9B82D8',
            'text' => '#454052'
        ],
        'Pastel Pink' => [
            'primary' => '#E57399',
            'secondary' => '#F8DCE8',
            'background' => '#FFF0F5',
            'card' => '#FFFFFF',
            'button' => '#E57399',
            'text' => '#453840'
        ],
        'Baby Blue' => [
            'primary' => '#5B92E5',
            'secondary' => '#DCEBFF',
            'background' => '#F0F5FC',
            'card' => '#FFFFFF',
            'button' => '#5B92E5',
            'text' => '#323E4D'
        ],
        'Sage Green' => [
            'primary' => '#7E9F7A',
            'secondary' => '#DCE8DB',
            'background' => '#F3F7F2',
            'card' => '#FFFFFF',
            'button' => '#7E9F7A',
            'text' => '#384236'
        ],
        'Cream' => [
            'primary' => '#B8936D',
            'secondary' => '#F2E4D5',
            'background' => '#FFFDF9',
            'card' => '#FFFFFF',
            'button' => '#B8936D',
            'text' => '#4A4139'
        ]
    ];
}

/**
 * Daftar font web-safe yang didukung
 */
function getSupportedFonts(): array {
    return [
        'Times New Roman, Times, Georgia, serif' => 'Times New Roman (Klasik & Elegan - Default)',
        'Georgia, Times, serif' => 'Georgia (Elegan & Mudah Dibaca)',
        'Garamond, Baskerville, serif' => 'Garamond (Halus & Berkarakter)',
        'Arial, Helvetica, sans-serif' => 'Arial (Modern & Bersih)',
        'Verdana, Geneva, sans-serif' => 'Verdana (Jelas & Terbuka)',
        'Trebuchet MS, Lucida Sans, sans-serif' => 'Trebuchet MS (Dinamis & Ringan)'
    ];
}

/**
 * Mengambil pengaturan tampilan aktif dari database
 */
function getThemeSettings(): array {
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }

    $default = [
        'preset_name' => 'Pastel Lilac',
        'color_primary' => '#9B82D8',
        'color_secondary' => '#DCCEF9',
        'color_background' => '#F5F0FF',
        'color_card' => '#FFFFFF',
        'color_button' => '#9B82D8',
        'color_text' => '#454052',
        'font_family' => 'Times New Roman, Times, Georgia, serif',
        'font_size_scale' => 'normal',
        'app_name' => 'KosCare',
        'app_tagline' => 'Perawatan Kos Jadi Lebih Mudah',
        'logo_path' => null,
        'logo_size' => 'medium',
        'card_radius' => '1rem',
        'button_shape' => 'pill'
    ];

    try {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM `theme_settings` WHERE `id` = 1 LIMIT 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $cached = array_merge($default, $row);
            return $cached;
        }
    } catch (Throwable $e) {
        // Fallback jika tabel belum terbentuk
    }

    $cached = $default;
    return $cached;
}

/**
 * Mengambil nama aplikasi dinamis
 */
function getAppName(): string {
    $s = getThemeSettings();
    return !empty($s['app_name']) ? $s['app_name'] : 'KosCare';
}

/**
 * Mengambil tagline dinamis
 */
function getAppTagline(): string {
    $s = getThemeSettings();
    return !empty($s['app_tagline']) ? $s['app_tagline'] : 'Perawatan Kos Jadi Lebih Mudah';
}

/**
 * Mengambil URL logo atau null
 */
function getAppLogo(): ?string {
    $s = getThemeSettings();
    if (!empty($s['logo_path'])) {
        return BASE_URL . '/uploads/logos/' . basename($s['logo_path']);
    }
    return null;
}

/**
 * Ukuran tinggi logo dalam piksel
 */
function getLogoHeightPx(string $size): int {
    switch ($size) {
        case 'small': return 28;
        case 'large': return 48;
        case 'medium':
        default: return 36;
    }
}

/**
 * Render style tag CSS dinamis yang diaplikasikan ke seluruh halaman
 */
function renderThemeStyles(): void {
    $s = getThemeSettings();

    $fontFamily = !empty($s['font_family']) ? $s['font_family'] : 'Times New Roman, Times, Georgia, serif';
    $fontSizeBase = '16px';
    if (($s['font_size_scale'] ?? '') === 'large') {
        $fontSizeBase = '17px';
    } elseif (($s['font_size_scale'] ?? '') === 'compact') {
        $fontSizeBase = '15px';
    }

    $btnRadius = '9999px';
    if (($s['button_shape'] ?? '') === 'rounded') {
        $btnRadius = '10px';
    } elseif (($s['button_shape'] ?? '') === 'square') {
        $btnRadius = '4px';
    }

    $cardRadius = !empty($s['card_radius']) ? $s['card_radius'] : '1rem';
    ?>
    <style id="koscare-dynamic-theme">
        :root {
            --color-primary-purple: <?php echo htmlspecialchars($s['color_button'] ?? '#9B82D8'); ?>;
            --color-primary-purple-hover: <?php echo htmlspecialchars($s['color_primary'] ?? '#886BC8'); ?>;
            --color-pastel-lilac: <?php echo htmlspecialchars($s['color_secondary'] ?? '#DCCEF9'); ?>;
            --color-light-lilac: <?php echo htmlspecialchars($s['color_secondary'] ?? '#EEE7FF'); ?>;
            --color-soft-lavender: <?php echo htmlspecialchars($s['color_background'] ?? '#F5F0FF'); ?>;
            --color-pure-white: <?php echo htmlspecialchars($s['color_card'] ?? '#FFFFFF'); ?>;
            --color-charcoal: <?php echo htmlspecialchars($s['color_text'] ?? '#454052'); ?>;
            --app-font-family: <?php echo $fontFamily; ?>;
            --app-card-radius: <?php echo htmlspecialchars($cardRadius); ?>;
            --app-btn-radius: <?php echo htmlspecialchars($btnRadius); ?>;
        }

        html {
            font-size: <?php echo $fontSizeBase; ?>;
        }

        *, *::before, *::after, body, button, input, textarea, select, .nav-link, .btn, .card-title, h1, h2, h3, h4, h5, h6 {
            font-family: <?php echo $fontFamily; ?> !important;
        }

        body {
            background-color: var(--color-soft-lavender) !important;
            color: var(--color-charcoal) !important;
        }

        .card-aesthetic, .card {
            border-radius: var(--app-card-radius) !important;
            background-color: var(--color-pure-white) !important;
            border-color: var(--color-pastel-lilac) !important;
        }

        .navbar-custom {
            border-bottom: 1.5px solid var(--color-pastel-lilac) !important;
        }

        .btn-primary-purple, .btn-primary {
            background-color: var(--color-primary-purple) !important;
            border-color: var(--color-primary-purple) !important;
            border-radius: var(--app-btn-radius) !important;
            color: #FFFFFF !important;
        }

        .btn-primary-purple:hover, .btn-primary:hover {
            filter: brightness(0.92);
        }

        .btn-outline-purple {
            border-color: var(--color-pastel-lilac) !important;
            color: var(--color-charcoal) !important;
            border-radius: var(--app-btn-radius) !important;
        }

        .btn-outline-purple:hover {
            background-color: var(--color-pastel-lilac) !important;
        }

        .btn-pastel-pink {
            background-color: var(--color-pastel-lilac) !important;
            color: var(--color-charcoal) !important;
            border-radius: var(--app-btn-radius) !important;
            border-color: var(--color-pastel-lilac) !important;
        }

        .form-control, .form-select {
            border-color: var(--color-pastel-lilac) !important;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--color-primary-purple) !important;
            box-shadow: 0 0 0 0.25rem rgba(0, 0, 0, 0.08) !important;
        }

        .badge-status-diproses {
            background-color: var(--color-pastel-lilac) !important;
            color: var(--color-charcoal) !important;
        }
    </style>
    <?php
}
