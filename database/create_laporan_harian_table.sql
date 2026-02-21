-- SQL untuk membuat tabel laporan_harian
-- Copy paste ke phpMyAdmin

CREATE TABLE IF NOT EXISTS `laporan_harian` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `tanggal` DATE NOT NULL UNIQUE,
    `total_transaksi` INT NOT NULL DEFAULT 0,
    `barang_terjual` INT NOT NULL DEFAULT 0,
    `total_penjualan` DECIMAL(15, 2) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
