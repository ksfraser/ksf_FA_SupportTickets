<?php
/**
 * ksf_FA_SupportTickets — single front controller.
 *
 * @package ksf_FA_SupportTickets
 * @since   1.0.0
 */

chdir(__DIR__);

if (file_exists(__DIR__ . '/bootstrap.php')) {
    require_once __DIR__ . '/bootstrap.php';
}

$path_to_root = "../..";

$frontController = new \Ksfraser\FrontAccounting\SupportTickets\App\SupportTicketsFrontController(
    new \Ksfraser\FrontAccounting\SupportTickets\App\SupportTicketsAppShell(),
    'SA_ksf_FA_SupportTicketsVIEW',
    $path_to_root
);

$page_security = $frontController->getSecurityArea();
include_once($path_to_root . "/includes/session.inc");
add_access_extensions();

include_once($path_to_root . "/includes/ui.inc");

$frontController->run();
