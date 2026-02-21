-- SQL untuk membuat tabel transaksi
-- Copy paste HANYA isi file ini ke phpMyAdmin

DROP TABLE IF EXISTS `transaksi_details`;
DROP TABLE IF EXISTS `transaksi`;
DROP TABLE IF EXISTS `payment_settings`;

CREATE TABLE `transaksi` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nomor_transaksi` VARCHAR(255) NOT NULL UNIQUE,
    `tanggal` DATE NOT NULL,
    `sub_total` DECIMAL(15, 2) NOT NULL,
    `payment_method` ENUM('cash', 'qris') NOT NULL DEFAULT 'cash',
    `kasir_name` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `transaksi_details` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `transaksi_id` BIGINT UNSIGNED NOT NULL,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `quantity` INT NOT NULL,
    `price` DECIMAL(15, 2) NOT NULL,
    `total` DECIMAL(15, 2) NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `payment_settings` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(255) NOT NULL UNIQUE,
    `setting_value` TEXT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default QRIS setting
INSERT INTO `payment_settings` (`setting_key`, `setting_value`, `created_at`, `updated_at`) 
VALUES ('qris_image', NULL, NOW(), NOW());
