-- ============================================
-- SQL Script: Menambahkan Fitur Kuota Diskon / Stok Promo
-- Database: pos
-- Tanggal: 2026-02-07
-- ============================================

-- 1. Tambahkan kolom discount_stock (Stok Promo)
-- Kolom ini bersifat NULLABLE.
-- NULL = Unlimited (Diskon berlaku selama stok utama ada)
-- Angka (>0) = Kuota terbatas (misal hanya 10 barang pertama)

ALTER TABLE `products` 
ADD COLUMN `discount_stock` INT DEFAULT NULL COMMENT 'Kuota Stok Promo' AFTER `final_price`;

-- ============================================
-- Selesai!
-- ============================================
