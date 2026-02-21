-- Tambahkan kolom category ke tabel products
ALTER TABLE `products` 
ADD COLUMN `category` VARCHAR(255) NULL AFTER `name`;
