<?php
/**
 * Support Ticket Detail/Edit Page
 */

$page_security = 'ST_VIEW_TICKET';
$path_to_root = "../../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/FA_SupportTickets/includes/st_db.inc");

page(_($help_context = "Support Ticket Detail"));

simple_page_mode(true);

//-----------------------------------------------------------------------------------

$selected_id = $selected_id ?? 0;
if ($selected_id == 0 && isset($_GET['ticket_id'])) {
    $selected_id = $_GET['ticket_id'];
}

if ($selected_id > 0) {
    $ticket = get_ticket($selected_id);
    if ($ticket) {
        $_POST = array_merge($_POST, $ticket);
    }
}

if ($Mode == 'UPDATE_ITEM') {
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
            'status' => $_POST['status'],
            'assigned_to' => $_POST['assigned_to'],
            'resolution' => $_POST['resolution'],
        ];
        update_ticket($selected_id, $ticket_data);
        
        add_ticket_activity($selected_id, 'Updated', 'Ticket updated by ' . $_SESSION['wa_current_user']->name);
        
        display_notification(_('Ticket updated'));
        $Mode = 'RESET';
    }
}

//--------------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE, "width=80%");
table_section_title(_("Ticket Information"));

if ($selected_id > 0) {
    label_row(_("Ticket Number:"), $ticket['ticket_number'] ?? '');
    label_row(_("Created:"), $ticket['created_at'] ?? '');
}
text_row_ex(_("Subject:"), 'subject', 60, '', '', '', '');
textarea_row(_("Description:"), 'description', $_POST['description'], 40, 5);

$types = ['Question' => _('Question'), 'Issue' => _('Issue'), 'Request' => _('Request'), 'Bug' => _('Bug')];
$priorities = ['Low' => _('Low'), 'Medium' => _('Medium'), 'High' => _('High'), 'Critical' => _('Critical')];
$statuses = ['New' => _('New'), 'InProgress' => _('In Progress'), 'Waiting' => _('Waiting'), 'Resolved' => _('Resolved'), 'Closed' => _('Closed')];

select_row(_("Type:"), 'type', $_POST['type'] ?? 'Question', $types);
select_row(_("Priority:"), 'priority', $_POST['priority'] ?? 'Medium', $priorities);
select_row(_("Status:"), 'status', $_POST['status'] ?? 'New', $statuses);
text_row_ex(_("Assigned To:"), 'assigned_to', 30, '', '', '', '');

end_table();

if ($selected_id > 0) {
    submit_center('Update', _("Update Ticket"), true, '', true);
}

end_form();

//--------------------------------------------------------------------------------

if ($selected_id > 0) {
    echo '<br><h3>' . _('Ticket Activities') . '</h3>';
    
    $activities = get_ticket_activities($selected_id);
    
    start_table(TABLESTYLE, "width=80%");
    table_header([
        _("Date"), _("Action"), _("Performed By"), _("Notes")
    ]);
    
    foreach ($activities as $act) {
        label_cell(sql2date($act['created_at']));
        label_cell($act['action']);
        label_cell($act['performed_by']);
        label_cell($act['notes']);
        end_row();
    }
    end_table();
    
    echo '<br><h3>' . _('Add Activity') . '</h3>';
    start_form();
    start_table(TABLESTYLE, "width=80%");
    text_row_ex(_("Action:"), 'activity_action', 30);
    textarea_row(_("Notes:"), 'activity_notes', '', 30, 4);
    end_table();
    submit_center('add_activity', _("Add Activity"));
    end_form();
    
    echo '<br><h3>' . _('Ticket Notes') . '</h3>';
    
    $notes = get_ticket_notes($selected_id);
    
    start_table(TABLESTYLE, "width=80%");
    table_header([
        _("Date"), _("Note Type"), _("Note"), _("Created By")
    ]);
    
    foreach ($notes as $note) {
        label_cell(sql2date($note['created_at']));
        label_cell($note['note_type']);
        label_cell($note['note']);
        label_cell($note['created_by']);
        end_row();
    }
    end_table();
    
    echo '<br><h3>' . _('Add Note') . '</h3>';
    start_form();
    start_table(TABLESTYLE, "width=80%");
    $note_types = ['Comment' => _('Comment'), 'Internal' => _('Internal'), 'Public' => _('Public')];
    select_row(_("Note Type:"), 'note_type', 'Comment', $note_types);
    textarea_row(_("Note:"), 'note', '', 30, 4);
    end_table();
    submit_center('add_note', _("Add Note"));
    end_form();
}

end_page();