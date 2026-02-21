-- Memastikan semua kolom timestamp dan image ada
ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `products` ADD COLUMN IF NOT EXISTS `image` VARCHAR(255) NULL AFTER `stock`;
