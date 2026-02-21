-- ============================================
-- SQL Script: Menambahkan Fitur Diskon
-- Database: pos (Point of Sale)
-- Tanggal: 2026-02-07
-- ============================================

-- 1. Menambahkan kolom diskon ke tabel products
ALTER TABLE `products` 
ADD COLUMN `discount_percentage` DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Persentase Diskon (0-100)' AFTER `selling_price`,
ADD COLUMN `discount_amount` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Nominal Diskon dalam Rupiah' AFTER `discount_percentage`,
ADD COLUMN `final_price` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Harga Setelah Diskon' AFTER `discount_amount`;

-- 2. Update produk yang sudah ada agar memiliki final_price = selling_price
UPDATE `products` 
SET `final_price` = `selling_price`,
    `discount_percentage` = 0,
    `discount_amount` = 0
WHERE `final_price` = 0 OR `final_price` IS NULL;

-- 3. Verifikasi hasil (Optional - untuk pengecekan)
-- SELECT id, product_code, name, selling_price, discount_percentage, discount_amount, final_price 
-- FROM products 
-- LIMIT 10;

-- ============================================
-- SELESAI!
-- Kolom diskon berhasil ditambahkan.
-- ============================================
