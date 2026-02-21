-- COPY PASTE SCRIPT INI KE phpMyAdmin
-- Database: pos

-- Step 1: Tambah Kolom Diskon dan Stok Promo
ALTER TABLE `products` 
ADD COLUMN `discount_percentage` DECIMAL(5,2) DEFAULT 0.00 AFTER `selling_price`,
ADD COLUMN `discount_amount` DECIMAL(15,2) DEFAULT 0.00 AFTER `discount_percentage`,
ADD COLUMN `final_price` DECIMAL(15,2) DEFAULT 0.00 AFTER `discount_amount`,
ADD COLUMN `discount_stock` INT DEFAULT NULL COMMENT 'Kuota Stok Promo' AFTER `final_price`;

-- Step 2: Update Data Lama
UPDATE `products` 
SET `final_price` = `selling_price`,
    `discount_percentage` = 0,
    `discount_amount` = 0,
    `discount_stock` = NULL
WHERE `final_price` = 0 OR `final_price` IS NULL;
