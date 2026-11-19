<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\SupportTickets\App;

use ksfraser\FrontAccounting\Common\App\AbstractAppShell;
use ksfraser\FrontAccounting\Common\Menu\FAModuleMenu;
use Ksfraser\FrontAccounting\SupportTickets\Controller\DashboardTabController;
use Ksfraser\FrontAccounting\SupportTickets\Controller\TicketCommentsTabController;
use Ksfraser\FrontAccounting\SupportTickets\Controller\TicketsTabController;

/**
 * SupportTicketsAppShell — application shell SRP.
 *
 * @package ksf_FA_SupportTickets
 * @since   1.0.0
 */
class SupportTicketsAppShell extends AbstractAppShell
{
    public function __construct()
    {
        parent::__construct('support', 'index.php', 'view', 'tickets');

        $this->add_module(_("Support Tickets"));

        $menu = new FAModuleMenu(
            'modules/ksf_FA_SupportTickets/index.php',
            'view',
            ''
        );

        $menu->addItem('tickets', _("Tickets"), MENU_INQUIRY)
             ->registerWithApp($this, 'SA_ksf_FA_SupportTicketsVIEW');
        $menu->addItem('support_dashboard', _("Support Dashboard"), MENU_INQUIRY)
             ->registerWithApp($this, 'SA_ksf_FA_SupportTicketsVIEW');
        $menu->addItem('ticket_comments', _("Ticket Comments"), MENU_INQUIRY)
             ->registerWithApp($this, 'SA_ksf_FA_SupportTicketsVIEW');

        $this->add_extensions();
    }
}
