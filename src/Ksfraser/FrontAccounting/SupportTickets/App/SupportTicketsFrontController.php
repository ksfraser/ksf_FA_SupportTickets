<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\SupportTickets\App;

use ksfraser\FrontAccounting\Common\App\AbstractFrontController;

/**
 * SupportTicketsFrontController — single front controller.
 *
 * @package ksf_FA_SupportTickets
 * @since   1.0.0
 */
class SupportTicketsFrontController extends AbstractFrontController
{
    public function __construct(
        SupportTicketsAppShell $appShell,
        string $fallbackSecurityArea,
        string $path_to_root
    ) {
        parent::__construct($appShell, $fallbackSecurityArea, $path_to_root);
    }
}
