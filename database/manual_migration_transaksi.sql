-- Manual Migration untuk Tabel Transaksi
-- Jalankan SQL ini di phpMyAdmin atau HeidiSQL

-- Tabel Transaksi (Header)
CREATE TABLE IF NOT EXISTS `transaksi` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nomor_transaksi` VARCHAR(255) NOT NULL UNIQUE,
    `tanggal` DATE NOT NULL,
    `sub_total` DECIMAL(15, 2) NOT NULL,
    `kasir_name` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Transaksi Details (Item-item barang yang dibeli)
CREATE TABLE IF NOT EXISTS `transaksi_details` (
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
