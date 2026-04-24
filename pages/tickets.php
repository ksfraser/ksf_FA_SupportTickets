<?php
$page_security = 'ST_VIEW_TICKET';
$path_to_root = "../../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/FA_SupportTickets/includes/st_db.inc");

page(_($help_context = "Support Tickets"));

simple_page_mode(true);

//-----------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM')
{
    $input_error = 0;
    if (strlen($_POST['subject']) == 0) {
        $input_error = 1;
        display_error(_("Subject cannot be empty."));
    }
    if ($input_error != 1) {
        $ticket_data = [
            'subject' => $_POST['subject'],
            'description' => $_POST['description'],
            'type' => $_POST['type'],
            'priority' => $_POST['priority'],
            'debtor_no' => $_POST['debtor_no'],
            'contact_id' => $_POST['contact_id'],
            'warranty_id' => $_POST['warranty_id'],
            'assigned_to' => $_POST['assigned_to'],
            'team_id' => $_POST['team_id'],
            'status' => $_POST['status'],
            'created_by' => $_SESSION['wa_current_user']->name,
        ];
        if ($selected_id != -1) {
            update_ticket($selected_id, $ticket_data);
            display_notification(_('Ticket updated'));
        } else {
            add_ticket($ticket_data);
            display_notification(_('Ticket created'));
        }
        $Mode = 'RESET';
    }
}

if ($Mode == 'Delete') {
    delete_ticket($selected_id);
    display_notification(_('Ticket deleted'));
    $Mode = 'RESET';
}

if ($Mode == 'EDIT_ITEM') {
    $myrow = get_ticket($selected_id);
    if ($myrow) {
        $_POST = $myrow;
    }
}

if ($Mode == 'RESET') {
    $_POST['subject'] = '';
    $_POST['description'] = '';
    $_POST['type'] = 'Question';
    $_POST['priority'] = 'Medium';
    $_POST['status'] = 'New';
}

//-----------------------------------------------------------------------------------

$ticket_types = ['Question' => _('Question'), 'Issue' => _('Issue'), 'Request' => _('Request'), 'Bug' => _('Bug')];
$priorities = ['Low' => _('Low'), 'Medium' => _('Medium'), 'High' => _('High'), 'Critical' => _('Critical')];
$statuses = ['New' => _('New'), 'InProgress' => _('In Progress'), 'Waiting' => _('Waiting'), 'Resolved' => _('Resolved'), 'Closed' => _('Closed')];

start_form();

start_table(TABLESTYLE, "width=60%");

$heading = $Mode == 'EDIT_ITEM' ? _("Edit Ticket") : _("New Support Ticket");
table_section_title($heading);

text_row_ex(_("Subject:"), 'subject', 60, '', '', '', '');
textarea_row(_("Description:"), 'description', $_POST['description'], 30, 4);
select_row(_("Type:"), 'type', $_POST['type'], $ticket_types);
select_row(_("Priority:"), 'priority', $_POST['priority'], $priorities);
select_row(_("Status:"), 'status', $_POST['status'], $statuses);
debtor_row(_("Account:"), 'debtor_no', $_POST['debtor_no'], true);
smallint_row(_("Contact ID:"), 'contact_id', $_POST['contact_id']);
smallint_row(_("Warranty ID:"), 'warranty_id', $_POST['warranty_id']);
text_row_ex(_("Assigned To:"), 'assigned_to', 30, '', '', '', '');
smallint_row(_("Team ID:"), 'team_id', $_POST['team_id']);

end_table();

submit_center($Mode == 'EDIT_ITEM' ? _("Update") : _("Create Ticket"), true, '', true);

//--------------------------------------------------------------------------------

$sql = "SELECT t.id, t.ticket_number, t.subject, t.type, t.status, t.priority, t.state, 
               t.created_at, d.name as customer_name
        FROM " . TB_PREF . "fa_st_tickets t
        LEFT JOIN " . TB_PREF . "debtors_master d ON t.debtor_no = d.debtor_no
        ORDER BY t.created_at DESC";

$result = db_query($sql, "Could not get tickets");

start_table(TABLESTYLE, "width=60%");

table_header([
    _("ID"), _("Ticket #"), _("Subject"), _("Type"), _("Status"), _("Priority"), _("Account"), _("Created"), _("Actions")
]);

while ($row = db_fetch_assoc($result)) {
    $statustxt = _($row['status']);
    $prioritytxt = _($row['priority']);

    href_js_edit_link("?selected_id=" . $row['id'] . "&Mode=EDIT_ITEM", 'edit', _("Edit"));
    delete_button_center("?selected_id=" . $row['id'] . "&Mode=Delete", _("Delete"));

    end_row();
}

end_table();

end_form();

end_page();