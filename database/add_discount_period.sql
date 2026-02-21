ALTER TABLE `products` 
ADD COLUMN `discount_start` DATETIME NULL COMMENT 'Waktu mulai diskon' AFTER `discount_stock`,
ADD COLUMN `discount_end` DATETIME NULL COMMENT 'Waktu berakhir diskon' AFTER `discount_start`;
