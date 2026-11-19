<?php
declare(strict_types=1);

/**
 * ksf_FA_SupportTickets - FrontAccounting Module
 *
 * FA adapter over ksfraser/ksf-support-tickets standalone business logic.
 *
 * @package   ksfraser\FrontAccounting\SupportTickets
 * @author    Kevin Fraser
 * @version   2.4.19-0
 * @license   GPL-3.0
 *
 * @BABOK Related: FR-HD-001
 *
 * @hook      install           Called when module is installed.
 * @hook      uninstall         Called when module is deleted.
 * @hook      install_options   Register pages under Support app.
 * @hook      install_access    Define security areas.
 * @hook      activate_extension Install SQL schema.
 */

define('SS_ksf_FA_SupportTickets', 147 << 8);

$autoload = dirname(__FILE__) . '/vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}

use Ksfraser\FrontAccounting\SupportTickets\App\SupportTicketsAppShell;
use Ksfraser\FrontAccounting\SupportTickets\App\SupportTicketsFrontController;
use ksfraser\SupportTickets\Service\TicketService;
use ksfraser\SupportTickets\Service\TicketCommentService;
use ksfraser\SupportTickets\Service\SlaService;

class hooks_ksf_FA_SupportTickets extends hooks
{
    use \Ksfraser\Traits\HookQueryProviderTrait;

    var $module_name = 'ksf_FA_SupportTickets';
    var $version     = '2.4.19-0';

    protected function _getAdvertisedValues(): array
    {
        return [
            'support.hooks_version' => '1.0',
            'support.module_version' => '1.0.0',
            'support.features' => ['tickets', 'ticket_comments', 'sla', 'categories', 'ticket_workflow'],
        ];
    }

    public function getModuleConstants(&$data, $opts = null)
    {
        $constants = [
            'KSF_SUPPORT_TICKETS_MODULE_NAME' => $this->module_name,
            'KSF_SUPPORT_TICKETS_CAPABILITIES' => 'ticket_create,ticket_assign,ticket_resolve,ticket_close,ticket_add_note,sla_breach',
        ];
        $data['constants'] = $constants;
        $data['_module'] = $this->module_name;
        return $constants;
    }

    public function getModuleCapabilities(&$data, $opts = null)
    {
        $capabilities = [
            'ticket_create' => [
                'description' => 'Create support tickets',
                'methods' => ['CREATE_TICKET', 'TICKET_CREATED'],
            ],
            'ticket_assign' => [
                'description' => 'Assign support tickets to agents',
                'methods' => ['ASSIGN_TICKET', 'TICKET_ASSIGNED'],
            ],
            'ticket_resolve' => [
                'description' => 'Resolve support tickets',
                'methods' => ['RESOLVE_TICKET', 'TICKET_RESOLVED'],
            ],
            'ticket_close' => [
                'description' => 'Close support tickets',
                'methods' => ['CLOSE_TICKET', 'TICKET_CLOSED'],
            ],
            'ticket_add_note' => [
                'description' => 'Add notes/comments to tickets',
                'methods' => ['ADD_TICKET_NOTE', 'TICKET_NOTE_ADDED'],
            ],
            'sla_breach' => [
                'description' => 'SLA breach detection and notification',
                'methods' => ['CHECK_SLA_BREACH', 'SLA_BREACH'],
            ],
        ];
        $data['capabilities'] = $capabilities;
        $data['_module'] = $this->module_name;
        return $capabilities;
    }

    public function hasCapability(&$data, $opts = null)
    {
        $cap = $opts['capability'] ?? null;
        if ($cap === null) {
            return false;
        }
        $available = array_keys($this->getModuleCapabilities($data)['capabilities'] ?? []);
        return in_array($cap, $available, true);
    }

    public function respondToCapabilityRequest(&$data, $opts = null)
    {
        $cap = $opts['capability'] ?? null;
        if ($cap === null || !$this->hasCapability($data, $opts)) {
            $data['supported'] = false;
            return;
        }
        $data['supported'] = true;
        $data['module'] = $this->module_name;
    }

    /**
     * Hook responder: TICKET_ASSIGNED
     * Fired when a ticket is assigned to an agent.
     * Payload: ['ticket_id' => int, 'ticket_number' => string, 'assigned_to' => int, 'assigned_by' => int]
     *
     * @param array &$data
     * @param array|null $opts
     */
    public function TICKET_ASSIGNED(&$data, $opts = null)
    {
        if (!isset($data['ticket_id']) || !isset($data['assigned_to'])) {
            $data['handled'] = false;
            $data['error'] = 'Missing ticket_id or assigned_to';
            return;
        }
        hook_invoke_all('ticket_assigned', $data);
        $data['handled'] = true;
        $data['module'] = $this->module_name;
    }

    /**
     * Hook responder: TICKET_RESOLVED
     * Fired when a ticket is resolved.
     * Payload: ['ticket_id' => int, 'ticket_number' => string, 'resolved_by' => int]
     *
     * @param array &$data
     * @param array|null $opts
     */
    public function TICKET_RESOLVED(&$data, $opts = null)
    {
        if (!isset($data['ticket_id']) || !isset($data['resolved_by'])) {
            $data['handled'] = false;
            $data['error'] = 'Missing ticket_id or resolved_by';
            return;
        }
        hook_invoke_all('ticket_resolved', $data);
        $data['handled'] = true;
        $data['module'] = $this->module_name;
    }

    /**
     * Hook responder: TICKET_CLOSED
     * Fired when a ticket is closed.
     * Payload: ['ticket_id' => int, 'ticket_number' => string, 'closed_by' => int]
     *
     * @param array &$data
     * @param array|null $opts
     */
    public function TICKET_CLOSED(&$data, $opts = null)
    {
        if (!isset($data['ticket_id']) || !isset($data['closed_by'])) {
            $data['handled'] = false;
            $data['error'] = 'Missing ticket_id or closed_by';
            return;
        }
        hook_invoke_all('ticket_closed', $data);
        $data['handled'] = true;
        $data['module'] = $this->module_name;
    }

    /**
     * Hook responder: SLA_BREACH
     * Fired when a ticket's SLA is breached.
     * Payload: ['ticket_id' => int, 'ticket_number' => string, 'sla_id' => int, 'breach_hours' => float]
     *
     * @param array &$data
     * @param array|null $opts
     */
    public function SLA_BREACH(&$data, $opts = null)
    {
        if (!isset($data['ticket_id'])) {
            $data['handled'] = false;
            $data['error'] = 'Missing ticket_id';
            return;
        }
        hook_invoke_all('sla_breach', $data);
        $data['handled'] = true;
        $data['module'] = $this->module_name;
    }

    /**
     * Hook responder: TICKET_NOTE_ADDED
     * Fired when a note/comment is added to a ticket.
     * Payload: ['ticket_id' => int, 'comment_id' => int, 'author_id' => int, 'is_internal' => bool]
     *
     * @param array &$data
     * @param array|null $opts
     */
    public function TICKET_NOTE_ADDED(&$data, $opts = null)
    {
        if (!isset($data['ticket_id']) || !isset($data['comment_id'])) {
            $data['handled'] = false;
            $data['error'] = 'Missing ticket_id or comment_id';
            return;
        }
        hook_invoke_all('ticket_note_added', $data);
        $data['handled'] = true;
        $data['module'] = $this->module_name;
    }

    /**
     * Register module menus under Support application
     */
    function install_options($app) {
        if ($app->id == 'orders' && method_exists($this, 'add_module_app')) {
            $this->add_module_app('support_tickets', _("Tickets"), 'modules/ksf_FA_SupportTickets/index.php?view=tickets', SA_ksf_FA_SupportTicketsVIEW);
            $this->add_module_app('support_dashboard', _("Support Dashboard"), 'modules/ksf_FA_SupportTickets/index.php?view=dashboard', SA_ksf_FA_SupportTicketsVIEW);
            $this->add_module_app('support_ticket_comments', _("Ticket Comments"), 'modules/ksf_FA_SupportTickets/index.php?view=ticket_comments', SA_ksf_FA_SupportTicketsVIEW);
            $this->add_module_app('support_config', _("Support Config"), 'modules/ksf_FA_SupportTickets/index.php?view=config', SA_ksf_FA_SupportTicketsMANAGE);
        }
    }

    /**
     * Define security permission areas
     */
    function install_access() {
        $security_sections[SS_ksf_FA_SupportTickets] = _("Support Tickets");
        $security_areas['SA_ksf_FA_SupportTicketsVIEW'] = array(
            SS_ksf_FA_SupportTickets | 1,
            _("View Support Tickets")
        );
        $security_areas['SA_ksf_FA_SupportTicketsMANAGE'] = array(
            SS_ksf_FA_SupportTickets | 2,
            _("Manage Support Tickets Config")
        );
        $security_areas['SA_ksf_FA_SupportTicketsASSIGN'] = array(
            SS_ksf_FA_SupportTickets | 3,
            _("Assign Tickets")
        );
        $security_areas['SA_ksf_FA_SupportTicketsRESOLVE'] = array(
            SS_ksf_FA_SupportTickets | 4,
            _("Resolve Tickets")
        );
        $security_areas['SA_ksf_FA_SupportTicketsCLOSE'] = array(
            SS_ksf_FA_SupportTickets | 5,
            _("Close Tickets")
        );
        $security_areas['SA_ksf_FA_SupportTicketsADD_NOTE'] = array(
            SS_ksf_FA_SupportTickets | 6,
            _("Add Ticket Notes")
        );
        $security_areas['SA_ksf_FA_SupportTicketsREAD_ASSIGNED'] = array(
            SS_ksf_FA_SupportTickets | 7,
            _("View Tickets for Assigned Customers")
        );
        return array($security_areas, $security_sections);
    }

    /**
     * Install database schema on activation
     */
    function activate_extension($company, $check_only=true) {
        $this->ensure_composer_dependencies();

        if (!$this->ksf_fa_common_available()) {
            display_warning(_('Support Tickets requires the ksf-fa-common Composer package. Run composer install, then re-activate Support Tickets.'));
            return false;
        }

        if (!$check_only) {
            $this->install_common_table('ksf_modules.sql');
        }

        if (file_exists(dirname(__FILE__) . '/sql/install.sql')) {
            $updates = array(
                'ksf_hd_tickets.sql'               => array('hd_tickets'),
                'ksf_hd_ticket_comments.sql'       => array('hd_ticket_comments'),
                'ksf_hd_slas.sql'                  => array('hd_slas'),
                'ksf_hd_ticket_categories.sql'     => array('hd_ticket_categories'),
            );
            return $this->update_databases($company, $updates, $check_only);
        }

        return true;
    }

    private function ksf_fa_common_available()
    {
        $autoload = dirname(__FILE__) . '/vendor/autoload.php';
        if (!file_exists($autoload)) {
            return false;
        }
        return class_exists('ksfraser\\FrontAccounting\\Common\\Utils\\SchemaInstaller', false);
    }

    private function install_common_table(string $tableFile): bool
    {
        $vendorDir = dirname(__FILE__) . '/vendor/ksfraser/ksf-fa-common';
        $sqlPath   = $vendorDir . '/sql/' . $tableFile;

        if (!file_exists($sqlPath)) {
            error_log('KSF Module: common table SQL not found: ' . $sqlPath);
            return false;
        }

        $sql = file_get_contents($sqlPath);
        if ($sql === false) {
            return false;
        }

        $sql = preg_replace('/--[^\n]*/', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        $statements = array_map('trim', explode(';', $sql));

        $ok = true;
        foreach ($statements as $stmt) {
            $stmt = trim($stmt);
            if ($stmt === '') {
                continue;
            }
            if (!db_query($stmt, 'Common table install failed: ' . substr($stmt, 0, 80))) {
                $ok = false;
            }
        }
        return $ok;
    }

    private function ensure_composer_dependencies() {
        $module_dir = dirname(__FILE__);
        $autoload_path = $module_dir . '/vendor/autoload.php';

        if (file_exists($autoload_path)) {
            return;
        }

        $composer_path = $module_dir . '/composer.json';
        if (!file_exists($composer_path)) {
            return;
        }

        chdir($module_dir);
        $output = array();
        $return_code = 0;
        exec('composer install --no-interaction --prefer-dist 2>&1', $output, $return_code);
        if ($return_code !== 0) {
            error_log('KSF Module: composer install failed: ' . implode("\n", $output));
        }
    }
}
