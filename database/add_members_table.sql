-- 1. Create members table
CREATE TABLE `members` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `members_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Add columns to transaksi table
ALTER TABLE `transaksi` 
ADD COLUMN `member_id` bigint(20) unsigned NULL AFTER `kasir_name`,
ADD COLUMN `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00' AFTER `sub_total`,
ADD COLUMN `grand_total` decimal(15,2) NOT NULL DEFAULT '0.00' AFTER `discount_amount`;

-- 3. Add foreign key (Optional, recommended)
ALTER TABLE `transaksi`
ADD CONSTRAINT `transaksi_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL;
