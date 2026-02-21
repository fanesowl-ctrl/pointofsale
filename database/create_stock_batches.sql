CREATE TABLE `stock_batches` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `product_id` bigint(20) unsigned NOT NULL,
    `quantity` int(11) NOT NULL COMMENT 'Sisa stok',
    `original_quantity` int(11) NOT NULL COMMENT 'Stok awal',
    `cost_price` decimal(15,2) NOT NULL DEFAULT 0.00,
    `received_at` date NOT NULL,
    `expiry_date` date DEFAULT NULL,
    `batch_code` varchar(255) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `stock_batches_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
