-- ksf_FA_SupportTickets module database schema for FrontAccounting
-- Table: 0_ksf_hd_ticket_comments

CREATE TABLE IF NOT EXISTS `0_ksf_hd_ticket_comments` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `ticket_id` INT(11) NOT NULL,
    `author_id` INT(11) NOT NULL,
    `comment` TEXT NOT NULL,
    `is_internal` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `ticket_id` (`ticket_id`),
    KEY `author_id` (`author_id`),
    KEY `created_at` (`created_at`),
    CONSTRAINT `fk_hd_comments_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `0_ksf_hd_tickets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
