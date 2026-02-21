-- ============================================
-- SQL Script: Contoh Data Produk dengan Diskon
-- Database: pos (Point of Sale)
-- Tanggal: 2026-02-07
-- ============================================

-- Pastikan kolom diskon sudah ditambahkan terlebih dahulu
-- Jalankan file: add_discount_columns.sql

-- Insert contoh produk dengan berbagai tingkat diskon
INSERT INTO `products` (
    `product_code`, 
    `name`, 
    `cost_price`, 
    `selling_price`, 
    `discount_percentage`, 
    `discount_amount`, 
    `final_price`, 
    `stock`, 
    `created_at`, 
    `updated_at`
) VALUES
-- Produk dengan Diskon 10%
(
    'DISC001',
    'Produk Diskon 10%',
    50000.00,
    100000.00,
    10.00,
    10000.00,
    90000.00,
    50,
    NOW(),
    NOW()
),

-- Produk dengan Diskon 25%
(
    'DISC002',
    'Produk Diskon 25%',
    75000.00,
    150000.00,
    25.00,
    37500.00,
    112500.00,
    30,
    NOW(),
    NOW()
),

-- Produk dengan Diskon 50% (Flash Sale)
(
    'DISC003',
    'Produk Diskon 50% (Flash Sale)',
    100000.00,
    200000.00,
    50.00,
    100000.00,
    100000.00,
    10,
    NOW(),
    NOW()
),

-- Produk dengan Diskon 15%
(
    'DISC004',
    'Produk Diskon 15%',
    60000.00,
    120000.00,
    15.00,
    18000.00,
    102000.00,
    75,
    NOW(),
    NOW()
),

-- Produk Tanpa Diskon
(
    'NODISC001',
    'Produk Tanpa Diskon',
    60000.00,
    120000.00,
    0.00,
    0.00,
    120000.00,
    100,
    NOW(),
    NOW()
);

-- ============================================
-- Verifikasi data yang baru diinsert
-- ============================================
SELECT 
    product_code AS 'Kode',
    name AS 'Nama Produk',
    FORMAT(selling_price, 0, 'id_ID') AS 'Harga Normal',
    CONCAT(discount_percentage, '%') AS 'Diskon',
    FORMAT(discount_amount, 0, 'id_ID') AS 'Potongan',
    FORMAT(final_price, 0, 'id_ID') AS 'Harga Final',
    stock AS 'Stok'
FROM products
WHERE product_code LIKE 'DISC%' OR product_code LIKE 'NODISC%'
ORDER BY discount_percentage DESC;

-- ============================================
-- SELESAI!
-- Contoh produk dengan diskon berhasil ditambahkan.
-- ============================================
