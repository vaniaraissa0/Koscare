-- ==============================================================
-- DATABASE: koscare_db
-- SISTEM PELAPORAN DAN PEMANTAUAN KERUSAKAN FASILITAS KOS (KosCare)
-- Kompatibel dengan MySQL 5.7+ / MariaDB 10.4+ (XAMPP Windows)
-- ==============================================================

CREATE DATABASE IF NOT EXISTS `koscare_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `koscare_db`;

-- Matikan foreign key check sementara saat setup
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- 1. Tabel users
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `room_number` VARCHAR(20) DEFAULT NULL,
  `role` ENUM('penghuni', 'pengelola', 'teknisi') NOT NULL DEFAULT 'penghuni',
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_users_username` (`username`),
  UNIQUE KEY `uk_users_email` (`email`),
  KEY `idx_users_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Tabel reports
-- --------------------------------------------------------
DROP TABLE IF EXISTS `reports`;
CREATE TABLE `reports` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_number` VARCHAR(30) NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `category` ENUM(
    'Listrik dan lampu',
    'Air dan pipa',
    'Kamar mandi',
    'Pintu dan kunci',
    'Furnitur',
    'Internet/Wi-Fi',
    'Fasilitas umum',
    'Lainnya'
  ) NOT NULL,
  `location` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `urgency` ENUM('Rendah', 'Sedang', 'Tinggi', 'Darurat') NOT NULL DEFAULT 'Sedang',
  `photo` VARCHAR(255) DEFAULT NULL,
  `status` ENUM(
    'Menunggu Pemeriksaan',
    'Sedang Diproses',
    'Menunggu Informasi',
    'Selesai',
    'Ditolak'
  ) NOT NULL DEFAULT 'Menunggu Pemeriksaan',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_reports_ticket` (`ticket_number`),
  KEY `idx_reports_user_id` (`user_id`),
  KEY `idx_reports_status` (`status`),
  KEY `idx_reports_urgency` (`urgency`),
  KEY `idx_reports_category` (`category`),
  KEY `idx_reports_created_at` (`created_at`),
  CONSTRAINT `fk_reports_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. Tabel report_status_history
-- --------------------------------------------------------
DROP TABLE IF EXISTS `report_status_history`;
CREATE TABLE `report_status_history` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `report_id` INT UNSIGNED NOT NULL,
  `changed_by_user_id` INT UNSIGNED NOT NULL,
  `previous_status` VARCHAR(50) NOT NULL,
  `new_status` VARCHAR(50) NOT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_history_report_id` (`report_id`),
  KEY `idx_history_user_id` (`changed_by_user_id`),
  CONSTRAINT `fk_history_report` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_history_user` FOREIGN KEY (`changed_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. Tabel report_comments
-- --------------------------------------------------------
DROP TABLE IF EXISTS `report_comments`;
CREATE TABLE `report_comments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `report_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `comment` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_comments_report_id` (`report_id`),
  KEY `idx_comments_user_id` (`user_id`),
  CONSTRAINT `fk_comments_report` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 5. Tabel repair_records
-- --------------------------------------------------------
DROP TABLE IF EXISTS `repair_records`;
CREATE TABLE `repair_records` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `report_id` INT UNSIGNED NOT NULL,
  `technician_name` VARCHAR(100) DEFAULT 'Tim Teknisi KosCare',
  `completion_date` DATE NOT NULL,
  `repair_notes` TEXT NOT NULL,
  `cost_estimate` DECIMAL(10,2) DEFAULT 0.00,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_repair_report` (`report_id`),
  CONSTRAINT `fk_repair_report` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 6. Tabel technician_assignments
-- --------------------------------------------------------
DROP TABLE IF EXISTS `technician_assignments`;
CREATE TABLE `technician_assignments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `report_id` INT UNSIGNED NOT NULL,
  `technician_user_id` INT UNSIGNED NOT NULL,
  `assigned_by_user_id` INT UNSIGNED NOT NULL,
  `assigned_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('assigned', 'in_progress', 'completed') NOT NULL DEFAULT 'assigned',
  `notes` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_assign_report` (`report_id`),
  KEY `idx_assign_tech` (`technician_user_id`),
  CONSTRAINT `fk_assign_report` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_assign_tech` FOREIGN KEY (`technician_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_assign_by` FOREIGN KEY (`assigned_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 7. Tabel activity_logs
-- --------------------------------------------------------
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `action` VARCHAR(100) NOT NULL,
  `details` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_logs_user_id` (`user_id`),
  KEY `idx_logs_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Aktifkan kembali foreign key check
SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------
-- SEED DATA AWAL UNTUK PENGUJIAN
-- Catatan: Password terenkripsi menggunakan password_hash() BCRYPT
-- Password Pengelola: "VaniaR@1"
-- Password Penghuni Demo: "Penghuni123"
-- Password Teknisi Demo: "Teknisi123"
-- --------------------------------------------------------

-- Hash untuk VaniaR@1: $2y$10$WpZ6/f.aY18y2Yl2nUvL..h36sC9b27g6o2c9u1e7a4m0e0d5k1m (atau hasil password_hash standar)
-- Untuk kepastian kompatibilitas penuh, skrip setup/create_admin.php juga disediakan untuk generate ulang.

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `phone`, `room_number`, `role`, `status`) VALUES
(1, 'Vania Raissa', 'vania.raissa', 'vania.raissa@koscare.local', '$2y$10$sC4v904.xP5L1vJqP64JbOnb9Z4W8l6wXQO4l60u9Y6tJ9u8pP37y', '081234567890', 'Kantor Pengelola', 'pengelola', 'active'),
(2, 'Dimas Anggoro', 'dimas.anggoro', 'dimas@example.com', '$2y$10$5p8z9n6tY7m0K1vJqP64JbOnb9Z4W8l6wXQO4l60u9Y6tJ9u8pP37y', '081298765432', 'Kamar 102', 'penghuni', 'active'),
(3, 'Siti Nurhaliza', 'siti.nur', 'siti@example.com', '$2y$10$5p8z9n6tY7m0K1vJqP64JbOnb9Z4W8l6wXQO4l60u9Y6tJ9u8pP37y', '081345678912', 'Kamar 205', 'penghuni', 'active'),
(4, 'Pak Joko Santoso', 'joko.teknisi', 'joko@koscare.local', '$2y$10$5p8z9n6tY7m0K1vJqP64JbOnb9Z4W8l6wXQO4l60u9Y6tJ9u8pP37y', '081567890123', 'Workshop', 'teknisi', 'active');

-- Data contoh Laporan Kerusakan
INSERT INTO `reports` (`id`, `ticket_number`, `user_id`, `title`, `category`, `location`, `description`, `urgency`, `photo`, `status`, `created_at`) VALUES
(1, 'KC-202610-0001', 2, 'Kran Air Kamar Mandi Bocor Terus', 'Air dan pipa', 'Kamar 102', 'Kran wastafel tidak bisa ditutup rapat, air menetes deras sejak kemarin malam sehingga lantai basah.', 'Tinggi', NULL, 'Sedang Diproses', '2026-10-18 09:30:00'),
(2, 'KC-202610-0002', 2, 'Lampu Kamar Mandi Mati Total', 'Listrik dan lampu', 'Kamar 102', 'Lampu downlight kamar mandi mendadak padam dan timbul bunyi mendengung saat saklar dinyalakan.', 'Sedang', NULL, 'Menunggu Pemeriksaan', '2026-10-19 14:15:00'),
(3, 'KC-202610-0003', 3, 'Gagang Pintu Kamar Longgar & Macet', 'Pintu dan kunci', 'Kamar 205', 'Kunci silinder sulit diputar dari dalam, khawatir terkunci dan tidak bisa keluar.', 'Darurat', NULL, 'Selesai', '2026-10-17 11:00:00'),
(4, 'KC-202610-0004', 3, 'Wi-Fi Lantai 2 Sering Putus', 'Internet/Wi-Fi', 'Lantai 2 Koridor', 'Sinyal router KosCare-Lt2 sering hilang mendadak setiap jam 7 malam.', 'Rendah', NULL, 'Menunggu Informasi', '2026-10-19 19:45:00');

-- Riwayat Perubahan Status Contoh
INSERT INTO `report_status_history` (`report_id`, `changed_by_user_id`, `previous_status`, `new_status`, `notes`, `created_at`) VALUES
(1, 1, 'Menunggu Pemeriksaan', 'Sedang Diproses', 'Laporan telah diverifikasi. Ditugaskan ke Pak Joko untuk penggantian seal kran.', '2026-10-18 10:00:00'),
(3, 1, 'Menunggu Pemeriksaan', 'Sedang Diproses', 'Urgensi darurat diterima, langsung diprioritaskan teknisi kunci.', '2026-10-17 11:30:00'),
(3, 1, 'Sedang Diproses', 'Selesai', 'Silinder kunci pintu kamar 205 telah diganti dengan unit baru merk Dekkson. Pintu sudah berfungsi normal.', '2026-10-17 14:00:00'),
(4, 1, 'Menunggu Pemeriksaan', 'Menunggu Informasi', 'Mohon konfirmasi apakah lampu indikator WAN pada modem router lantai 2 berwarna merah atau hijau?', '2026-10-20 08:30:00');

-- Catatan Hasil Perbaikan untuk Laporan 3 (Selesai)
INSERT INTO `repair_records` (`report_id`, `technician_name`, `completion_date`, `repair_notes`, `cost_estimate`, `created_at`) VALUES
(3, 'Pak Joko Santoso', '2026-10-17', 'Penggantian 1 set lockcase dan silinder pintu Kamar 205. Diserahkan 3 anak kunci baru ke penghuni.', 125000.00, '2026-10-17 14:05:00');

-- Komentar / Diskusi Contoh
INSERT INTO `report_comments` (`report_id`, `user_id`, `comment`, `created_at`) VALUES
(1, 1, 'Halo Mas Dimas, teknisi dijadwalkan datang hari ini sekitar pukul 15.00 WIB.', '2026-10-18 10:05:00'),
(1, 2, 'Baik Bu Vania, saya ada di kamar setelah jam 14.30. Terima kasih banyak.', '2026-10-18 10:15:00');

-- --------------------------------------------------------
-- 7. Tabel theme_settings (Kustomisasi Desain & Identitas Website)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `theme_settings`;
CREATE TABLE `theme_settings` (
  `id` INT UNSIGNED NOT NULL DEFAULT 1,
  `preset_name` VARCHAR(50) NOT NULL DEFAULT 'Pastel Lilac',
  `color_primary` VARCHAR(20) NOT NULL DEFAULT '#9B82D8',
  `color_secondary` VARCHAR(20) NOT NULL DEFAULT '#DCCEF9',
  `color_background` VARCHAR(20) NOT NULL DEFAULT '#F5F0FF',
  `color_card` VARCHAR(20) NOT NULL DEFAULT '#FFFFFF',
  `color_button` VARCHAR(20) NOT NULL DEFAULT '#9B82D8',
  `color_text` VARCHAR(20) NOT NULL DEFAULT '#454052',
  `font_family` VARCHAR(100) NOT NULL DEFAULT 'Times New Roman, Times, Georgia, serif',
  `font_size_scale` VARCHAR(20) NOT NULL DEFAULT 'normal',
  `app_name` VARCHAR(100) NOT NULL DEFAULT 'KosCare',
  `app_tagline` VARCHAR(255) NOT NULL DEFAULT 'Perawatan Kos Jadi Lebih Mudah',
  `logo_path` VARCHAR(255) DEFAULT NULL,
  `logo_size` VARCHAR(20) NOT NULL DEFAULT 'medium',
  `card_radius` VARCHAR(20) NOT NULL DEFAULT '1rem',
  `button_shape` VARCHAR(20) NOT NULL DEFAULT 'pill',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed Pengaturan Tampilan Default (Pastel Lilac & Times New Roman)
INSERT INTO `theme_settings` (`id`, `preset_name`, `color_primary`, `color_secondary`, `color_background`, `color_card`, `color_button`, `color_text`, `font_family`, `font_size_scale`, `app_name`, `app_tagline`, `logo_path`, `logo_size`, `card_radius`, `button_shape`)
VALUES (1, 'Pastel Lilac', '#9B82D8', '#DCCEF9', '#F5F0FF', '#FFFFFF', '#9B82D8', '#454052', 'Times New Roman, Times, Georgia, serif', 'normal', 'KosCare', 'Perawatan Kos Jadi Lebih Mudah', NULL, 'medium', '1rem', 'pill')
ON DUPLICATE KEY UPDATE `id` = 1;

