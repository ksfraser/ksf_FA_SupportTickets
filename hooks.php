<?php
/**
 * FA_SupportTickets Module Hooks for FrontAccounting
 */

define('SS_SUPPORT', 136 << 8);

class hooks_fa_supporttickets extends hooks {
    var $module_name = 'fa_supporttickets';

    function install_options($app) {
        global $path_to_root;

        switch($app->id) {
            case 'CRM':
                $app->add_lapp_function(0, _("Support Tickets"),
                    $path_to_root."/modules/".$this->module_name."/tickets.php", 'SA_STVIEW', MENU_ENTRY);
                $app->add_lapp_function(1, _("Create Ticket"),
                    $path_to_root."/modules/".$this->module_name."/create.php", 'SA_STCREATE', MENU_ENTRY);
                $app->add_lapp_function(2, _("Ticket Types"),
                    $path_to_root."/modules/".$this->module_name."/types.php", 'SA_STMANAGE', MENU_MAINTENANCE);
                $app->add_rapp_function(3, _("Teams"),
                    $path_to_root."/modules/".$this->module_name."/teams.php", 'SA_STMANAGE', MENU_ENTRY);
                break;
        }
    }

    function install_access() {
        $security_sections[SS_SUPPORT] = _("Support Tickets");
        $security_areas['SA_STVIEW'] = array(SS_SUPPORT | 1, _("View Tickets"));
        $security_areas['SA_STCREATE'] = array(SS_SUPPORT | 2, _("Create Tickets"));
        $security_areas['SA_STMANAGE'] = array(SS_SUPPORT | 3, _("Manage Tickets"));
        return array($security_areas, $security_sections);
    }

    function activate_extension($company, $check_only=true) {
        $updates = array('sql/update.sql' => array($this->module_name));
        $ok = $this->update_databases($company, $updates, $check_only);
        if ($check_only || !$ok) {
            return $ok;
        }
        $this->ensure_support_schema();
        return $ok;
    }

    private function table_exists($table) {
        $sql = "SHOW TABLES LIKE " . db_escape($table);
        $res = db_query($sql, 'Failed checking table existence');
        return db_num_rows($res) > 0;
    }

    private function ensure_support_schema() {
        $tables = array(
            TB_PREF . "fa_st_tickets" => "
                CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_st_tickets` (
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
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            TB_PREF . "fa_st_tickets_activities" => "
                CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_st_tickets_activities` (
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
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            TB_PREF . "fa_st_tickets_notes" => "
                CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_st_tickets_notes` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `ticket_id` INT(11) NOT NULL,
                    `note` TEXT NOT NULL,
                    `note_type` VARCHAR(20) DEFAULT 'General',
                    `created_by` VARCHAR(100) DEFAULT NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_ticket_id` (`ticket_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            TB_PREF . "fa_st_tickets_items" => "
                CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_st_tickets_items` (
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
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            TB_PREF . "fa_st_teams" => "
                CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_st_teams` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(50) NOT NULL,
                    `leader_id` VARCHAR(100) DEFAULT NULL,
                    `inactive` TINYINT(1) DEFAULT 0,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            TB_PREF . "fa_st_ticket_types" => "
                CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_st_ticket_types` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(30) NOT NULL,
                    `description` VARCHAR(100) DEFAULT NULL,
                    `requires_project` TINYINT(1) DEFAULT 0,
                    `inactive` TINYINT(1) DEFAULT 0,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `idx_name` (`name`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        foreach ($tables as $table_name => $sql) {
            db_query($sql, "Could not create Support Tickets table: $table_name");
        }

        $this->insert_initial_ticket_data();
    }

    private function insert_initial_ticket_data() {
        $types = array(
            array('Question', 'Customer question about product/service', 0),
            array('Issue', 'Product or service issue', 1),
            array('Request', 'Service request', 0),
            array('Bug', 'Bug report', 1),
        );
        foreach ($types as $type) {
            db_query("INSERT IGNORE INTO " . TB_PREF . "fa_st_ticket_types (name, description, requires_project) 
                VALUES ('" . db_escape($type[0]) . "', '" . db_escape($type[1]) . "', " . $type[2] . ")");
        }

        $teams = array(
            array('Support Team', null),
            array('Technical Support', null),
            array('Billing', null),
        );
        foreach ($teams as $team) {
            db_query("INSERT IGNORE INTO " . TB_PREF . "fa_st_teams (name) 
                VALUES ('" . db_escape($team[0]) . "')");
        }
    }

    function db_prevoid($trans_type, $trans_no) {
        // Handle voiding if needed
    }
}
?>
