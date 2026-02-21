-- Menambahkan kolom image ke tabel products
ALTER TABLE `products` ADD COLUMN `image` VARCHAR(255) NULL AFTER `stock`;
