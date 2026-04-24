<?php
/**
 * FA_SupportTickets Module Hooks for FrontAccounting
 *
 * Support Tickets/Cases module - integrates with ksf-supporttickets
 */

$module_name = 'FA_SupportTickets';
$module_version = '1.0.0';
$module_description = 'Support Tickets (Cases) - warranty claims, issue tracking';
$module_author = 'KSFII Development Team';
$module_category = 'CRM';

function fa_st_install(): bool
{
    global $db;

    @include_once __DIR__ . '/vendor-src/Ksfraser/Common/ComposerDependencyManager.php';
    if (class_exists('Ksfraser\Common\ComposerDependencyManager')) {
        $composerMgr = new \Ksfraser\Common\ComposerDependencyManager(__DIR__);
        $composerMgr->ensureDependencies();
        @include_once $composerMgr->getAutoloadPath();
    }

    if (!fa_st_create_tables()) return false;
    if (!fa_st_insert_initial_data()) return false;
    return true;
}

function fa_st_activate(): bool
{
    @include_once __DIR__ . '/vendor-src/Ksfraser/Common/ComposerDependencyManager.php';
    if (class_exists('Ksfraser\Common\ComposerDependencyManager')) {
        $composerMgr = new \Ksfraser\Common\ComposerDependencyManager(__DIR__);
        $composerMgr->ensureDependencies();
        @include_once $composerMgr->getAutoloadPath();
    }

    add_hook('ticket_created', 'fa_st_on_ticket_created');
    add_hook('ticket_updated', 'fa_st_on_ticket_updated');
    add_hook('ticket_closed', 'fa_st_on_ticket_closed');
    return true;
}

function fa_st_deactivate(): bool { return true; }
function fa_st_uninstall(): bool { return true; }

function fa_st_create_tables(): bool
{
    global $db;

    $tables = [
        "CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_st_tickets` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `ticket_number` VARCHAR(30) NOT NULL,
            `subject` VARCHAR(255) NOT NULL,
            `description` TEXT,
            `type` VARCHAR(20) DEFAULT 'Question',
            `state` VARCHAR(20) DEFAULT 'Open',
            `status` VARCHAR(20) DEFAULT 'New',
            `priority` VARCHAR(20) DEFAULT 'Medium',
            `debtor_no` VARCHAR(20) DEFAULT NULL,
            `contact_id` INT(11) DEFAULT NULL,
            `warranty_id` INT(11) DEFAULT NULL,
            `assigned_to` VARCHAR(100) DEFAULT NULL,
            `team_id` INT(11) DEFAULT NULL,
            `project_id` INT(11) DEFAULT NULL,
            `invoice_id` INT(11) DEFAULT NULL,
            `resolution` TEXT,
            `created_by` VARCHAR(100) DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_ticket_number` (`ticket_number`),
            KEY `idx_debtor_no` (`debtor_no`),
            KEY `idx_status` (`status`),
            KEY `idx_priority` (`priority`),
            KEY `idx_assigned_to` (`assigned_to`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_st_tickets_activities` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `ticket_id` INT(11) NOT NULL,
            `activity_type` VARCHAR(20) NOT NULL,
            `direction` VARCHAR(10) DEFAULT 'outbound',
            `subject` VARCHAR(255) DEFAULT NULL,
            `message` TEXT,
            `email_from` VARCHAR(100) DEFAULT NULL,
            `email_to` VARCHAR(100) DEFAULT NULL,
            `phone_number` VARCHAR(20) DEFAULT NULL,
            `duration_minutes` INT(11) DEFAULT NULL,
            `assigned_to` VARCHAR(100) DEFAULT NULL,
            `scheduled_at` DATETIME DEFAULT NULL,
            `completed_at` DATETIME DEFAULT NULL,
            `status` VARCHAR(20) DEFAULT 'Completed',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_ticket_id` (`ticket_id`),
            KEY `idx_activity_type` (`activity_type`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_st_tickets_notes` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `ticket_id` INT(11) NOT NULL,
            `note` TEXT NOT NULL,
            `note_type` VARCHAR(20) DEFAULT 'General',
            `created_by` VARCHAR(100) DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_ticket_id` (`ticket_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_st_tickets_items` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `ticket_id` INT(11) NOT NULL,
            `item_type` VARCHAR(20) DEFAULT 'Service',
            `item_description` VARCHAR(255) NOT NULL,
            `quantity` DECIMAL(10,2) DEFAULT 1,
            `unit_price` DECIMAL(15,2) DEFAULT 0,
            `unit` VARCHAR(20) DEFAULT NULL,
            `invoice_id` INT(11) DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_ticket_id` (`ticket_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_st_teams` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(50) NOT NULL,
            `leader_id` VARCHAR(100) DEFAULT NULL,
            `inactive` TINYINT(1) DEFAULT 0,
            PRIMARY KEY (`id`)
        ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_st_ticket_types` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(30) NOT NULL,
            `description` VARCHAR(100) DEFAULT NULL,
            `requires_project` TINYINT(1) DEFAULT 0,
            `inactive` TINYINT(1) DEFAULT 0,
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_name` (`name`))
        ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    ];

    foreach ($tables as $sql) {
        if (!db_query($sql, "Could not create table")) return false;
    }
    return true;
}

function fa_st_insert_initial_data(): bool
{
    $types = [
        ['Question', 'Customer question about product/service', 0],
        ['Issue', 'Product or service issue', 1],
        ['Request', 'Service request', 0],
        ['Bug', 'Bug report', 1],
    ];
    foreach ($types as $type) {
        db_query("INSERT IGNORE INTO " . TB_PREF . "fa_st_ticket_types (name, description, requires_project) 
            VALUES ('" . db_escape($type[0]) . "', '" . db_escape($type[1]) . "', " . $type[2] . ")");
    }

    $teams = [
        ['Support Team', null],
        ['Technical Support', null],
        ['Billing', null],
    ];
    foreach ($teams as $team) {
        db_query("INSERT IGNORE INTO " . TB_PREF . "fa_st_teams (name) 
            VALUES ('" . db_escape($team[0]) . "')");
    }
    return true;
}

function fa_st_on_ticket_created($ticketId) { error_log("Ticket created: $ticketId"); }
function fa_st_on_ticket_updated($ticketId) { error_log("Ticket updated: $ticketId"); }
function fa_st_on_ticket_closed($ticketId) { error_log("Ticket closed: $ticketId"); }