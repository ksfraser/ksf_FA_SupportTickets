-- ksf_FA_SupportTickets module database schema for FrontAccounting
-- Table: 0_ksf_hd_slas

CREATE TABLE IF NOT EXISTS `0_ksf_hd_slas` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `response_hours` INT(11) NOT NULL DEFAULT 0,
    `resolve_hours` INT(11) NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
