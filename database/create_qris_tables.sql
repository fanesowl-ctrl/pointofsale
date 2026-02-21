-- 1. Membuat tabel payment_settings untuk menyimpan konfigurasi (misal gambar QRIS)
CREATE TABLE IF NOT EXISTS `payment_settings` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `setting_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `setting_value` text COLLATE utf8mb4_unicode_ci,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `payment_settings_setting_key_unique` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Memasukkan data awal (default) untuk pengaturan QRIS
INSERT INTO `payment_settings` (`setting_key`, `setting_value`, `created_at`, `updated_at`) 
VALUES ('qris_image', NULL, NOW(), NOW());

-- 3. Menambahkan kolom payment_method pada tabel transaksi (jika belum ada)
-- Note: Jalankan baris alter table ini hanya jika kolom payment_method belum ada
ALTER TABLE `transaksi` 
ADD COLUMN `payment_method` ENUM('cash', 'qris') NOT NULL DEFAULT 'cash' AFTER `sub_total`;
