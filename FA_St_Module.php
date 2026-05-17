<?php
/**
 * FA_SupportTickets Module
 */

$module_name = 'FA_SupportTickets';
$module_version = '1.0.0';
$module_description = 'Support Tickets (Cases) for FrontAccounting';
$module_author = 'KSFII Development Team';
$module_category = 'CRM';
$module_package = 'ksfraser/ksf-supporttickets';

define('ST_VIEW_TICKET', 'ST_VIEW_TICKET');
define('ST_MANAGE_TICKET', 'ST_MANAGE_TICKET');
define('ST_ASSIGN_TICKET', 'ST_ASSIGN_TICKET');
define('ST_ADMIN', 'ST_ADMIN');

function fa_st_module_init()
{
    global $fa_st_module;
    if (!isset($fa_st_module)) {
        $fa_st_module = new FA_St_Module();
    }
}

class FA_St_Module
{
    public function __construct()
    {
        $this->init_hooks();
    }

    private function init_hooks()
    {
        add_action('fa_init', array($this, 'on_fa_init'));
    }

    public function on_fa_init()
    {
        add_action('ticket_extra_fields', array($this, 'display_ticket_extra_fields'));
    }

    public function display_ticket_extra_fields($ticketId)
    {
        return;
    }
}

function fa_st_get_module_info()
{
    global $module_name, $module_version, $module_description, $module_author, $module_category, $module_package;

    return array(
        'name' => $module_name,
        'version' => $module_version,
        'description' => $module_description,
        'author' => $module_author,
        'category' => $module_category,
        'depends' => array('FA_CRM'),
        'package' => $module_package,
    );
}

function fa_st_get_menu_items()
{
    return array(
        array('title' => 'Support', 'heading' => true, 'order' => 50),
        array('title' => 'All Tickets', 'url' => '/modules/FA_SupportTickets/pages/tickets.php', 'access' => 'ST_VIEW_TICKET', 'parent' => 'Support', 'order' => 1),
        array('title' => 'My Tickets', 'url' => '/modules/FA_SupportTickets/pages/my_tickets.php', 'access' => 'ST_VIEW_TICKET', 'parent' => 'Support', 'order' => 2),
        array('title' => 'Teams', 'url' => '/modules/FA_SupportTickets/pages/teams.php', 'access' => 'ST_ADMIN', 'parent' => 'Support', 'order' => 10),
    );
}