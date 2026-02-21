-- ============================================
-- SQL DATA BARANG UNTUK POS SYSTEM
-- ============================================

-- 1. Tambahkan kolom image ke tabel products (jika belum ada)
ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `image` VARCHAR(255) NULL AFTER `stock`;

-- 2. Hapus data lama (opsional, hapus comment jika ingin reset data)
-- TRUNCATE TABLE `products`;

-- 3. Insert data barang contoh
INSERT INTO `products` (`product_code`, `name`, `cost_price`, `selling_price`, `stock`, `image`, `created_at`, `updated_at`) VALUES
-- Elektronik
('BRG001', 'Laptop ASUS ROG Strix G15', 12000000.00, 14500000.00, 15, NULL, NOW(), NOW()),
('BRG002', 'Mouse Logitech G502', 450000.00, 650000.00, 50, NULL, NOW(), NOW()),
('BRG003', 'Keyboard Mechanical RGB', 800000.00, 1200000.00, 30, NULL, NOW(), NOW()),
('BRG004', 'Monitor LG 24 Inch', 1500000.00, 2100000.00, 20, NULL, NOW(), NOW()),
('BRG005', 'Headset Gaming HyperX', 600000.00, 850000.00, 25, NULL, NOW(), NOW()),

-- Smartphone & Aksesoris
('BRG006', 'Samsung Galaxy S23', 8000000.00, 10500000.00, 12, NULL, NOW(), NOW()),
('BRG007', 'iPhone 14 Pro Max', 15000000.00, 18500000.00, 8, NULL, NOW(), NOW()),
('BRG008', 'Xiaomi Redmi Note 12', 2500000.00, 3200000.00, 40, NULL, NOW(), NOW()),
('BRG009', 'Powerbank Anker 20000mAh', 350000.00, 500000.00, 60, NULL, NOW(), NOW()),
('BRG010', 'Charger Fast Charging 65W', 150000.00, 250000.00, 80, NULL, NOW(), NOW()),

-- Komputer & Aksesoris
('BRG011', 'SSD Samsung 1TB NVMe', 1200000.00, 1650000.00, 35, NULL, NOW(), NOW()),
('BRG012', 'RAM DDR4 16GB Corsair', 800000.00, 1100000.00, 45, NULL, NOW(), NOW()),
('BRG013', 'Webcam Logitech C920', 900000.00, 1250000.00, 22, NULL, NOW(), NOW()),
('BRG014', 'Printer Canon Pixma', 1800000.00, 2400000.00, 10, NULL, NOW(), NOW()),
('BRG015', 'UPS APC 650VA', 650000.00, 900000.00, 18, NULL, NOW(), NOW()),

-- Audio & Video
('BRG016', 'Speaker Bluetooth JBL', 500000.00, 750000.00, 28, NULL, NOW(), NOW()),
('BRG017', 'Earbuds TWS Xiaomi', 250000.00, 400000.00, 55, NULL, NOW(), NOW()),
('BRG018', 'Microphone USB Blue Yeti', 1500000.00, 2000000.00, 12, NULL, NOW(), NOW()),
('BRG019', 'Action Camera GoPro Hero 11', 4500000.00, 6000000.00, 6, NULL, NOW(), NOW()),
('BRG020', 'Ring Light LED 12 Inch', 200000.00, 350000.00, 40, NULL, NOW(), NOW()),

-- Networking
('BRG021', 'Router WiFi 6 TP-Link', 800000.00, 1150000.00, 25, NULL, NOW(), NOW()),
('BRG022', 'Modem Fiber Optic', 300000.00, 500000.00, 30, NULL, NOW(), NOW()),
('BRG023', 'Switch Gigabit 8 Port', 400000.00, 600000.00, 20, NULL, NOW(), NOW()),
('BRG024', 'Kabel LAN Cat6 50M', 150000.00, 250000.00, 50, NULL, NOW(), NOW()),
('BRG025', 'WiFi Extender Repeater', 200000.00, 350000.00, 35, NULL, NOW(), NOW()),

-- Gaming
('BRG026', 'PlayStation 5 Console', 7000000.00, 9500000.00, 5, NULL, NOW(), NOW()),
('BRG027', 'Xbox Series X', 6500000.00, 8500000.00, 4, NULL, NOW(), NOW()),
('BRG028', 'Nintendo Switch OLED', 4000000.00, 5500000.00, 10, NULL, NOW(), NOW()),
('BRG029', 'Gaming Chair RGB', 2000000.00, 3000000.00, 8, NULL, NOW(), NOW()),
('BRG030', 'Gaming Desk 120cm', 1500000.00, 2200000.00, 12, NULL, NOW(), NOW()),

-- Aksesoris Komputer
('BRG031', 'Mousepad Gaming XXL', 100000.00, 180000.00, 70, NULL, NOW(), NOW()),
('BRG032', 'USB Hub 7 Port', 150000.00, 250000.00, 45, NULL, NOW(), NOW()),
('BRG033', 'Cooling Pad Laptop', 200000.00, 320000.00, 38, NULL, NOW(), NOW()),
('BRG034', 'Kabel HDMI 2.1 3M', 100000.00, 180000.00, 60, NULL, NOW(), NOW()),
('BRG035', 'Adaptor USB-C to HDMI', 150000.00, 250000.00, 42, NULL, NOW(), NOW()),

-- Storage
('BRG036', 'Flashdisk 64GB SanDisk', 80000.00, 150000.00, 100, NULL, NOW(), NOW()),
('BRG037', 'MicroSD 128GB Samsung', 150000.00, 250000.00, 85, NULL, NOW(), NOW()),
('BRG038', 'HDD External 2TB WD', 900000.00, 1300000.00, 25, NULL, NOW(), NOW()),
('BRG039', 'SSD External 500GB', 700000.00, 1000000.00, 30, NULL, NOW(), NOW()),
('BRG040', 'Card Reader USB 3.0', 50000.00, 100000.00, 90, NULL, NOW(), NOW()),

-- Software & Lisensi
('BRG041', 'Windows 11 Pro License', 1500000.00, 2000000.00, 50, NULL, NOW(), NOW()),
('BRG042', 'Microsoft Office 2021', 1200000.00, 1800000.00, 40, NULL, NOW(), NOW()),
('BRG043', 'Antivirus Kaspersky 1 Year', 300000.00, 500000.00, 60, NULL, NOW(), NOW()),
('BRG044', 'Adobe Creative Cloud 1 Month', 250000.00, 400000.00, 35, NULL, NOW(), NOW()),
('BRG045', 'Zoom Pro License 1 Year', 1500000.00, 2100000.00, 20, NULL, NOW(), NOW()),

-- Kabel & Konverter
('BRG046', 'Kabel USB-C to USB-C 2M', 80000.00, 150000.00, 75, NULL, NOW(), NOW()),
('BRG047', 'Kabel Lightning Apple', 150000.00, 250000.00, 55, NULL, NOW(), NOW()),
('BRG048', 'Konverter VGA to HDMI', 100000.00, 180000.00, 48, NULL, NOW(), NOW()),
('BRG049', 'Splitter Audio 3.5mm', 30000.00, 60000.00, 120, NULL, NOW(), NOW()),
('BRG050', 'Kabel AUX Audio 1.5M', 25000.00, 50000.00, 150, NULL, NOW(), NOW());

-- ============================================
-- CATATAN:
-- - Total 50 produk contoh
-- - Semua produk memiliki stok
-- - Harga sudah termasuk margin keuntungan
-- - Kolom image = NULL (bisa diupload nanti)
-- - Timestamp menggunakan NOW()
-- ============================================
