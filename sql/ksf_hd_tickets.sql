-- ksf_FA_SupportTickets module database schema for FrontAccounting
-- Table: 0_ksf_hd_tickets

CREATE TABLE IF NOT EXISTS `0_ksf_hd_tickets` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `ticket_number` VARCHAR(20) NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `category_id` INT(11) NOT NULL DEFAULT 0,
    `priority_id` INT(11) NOT NULL DEFAULT 0,
    `status_id` INT(11) NOT NULL DEFAULT 1,
    `sla_id` INT(11) NOT NULL DEFAULT 0,
    `assigned_to` INT(11) NOT NULL DEFAULT 0,
    `requester_id` INT(11) NOT NULL DEFAULT 0,
    `company_id` INT(11) NOT NULL DEFAULT 0,
    `due_date` DATETIME DEFAULT NULL,
    `resolved_at` DATETIME DEFAULT NULL,
    `closed_at` DATETIME DEFAULT NULL,
    `csat_rating` INT(11) NOT NULL DEFAULT 0,
    `csat_comment` TEXT,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_ticket_number` (`ticket_number`),
    KEY `status_id` (`status_id`),
    KEY `priority_id` (`priority_id`),
    KEY `assigned_to` (`assigned_to`),
    KEY `requester_id` (`requester_id`),
    KEY `category_id` (`category_id`),
    KEY `sla_id` (`sla_id`),
    KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
