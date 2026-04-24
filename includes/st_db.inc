<?php
/**
 * Support Tickets Database Functions
 */

// Try to load event manager
@include_once __DIR__ . '/../../vendor/ksfraser/event/src/Ksfraser/Event/EventManager.php';
$use_events = class_exists('Ksfraser\Event\EventManager');

function st_dispatch_event(string $eventName, $eventData = null): void
{
    if ($use_events && class_exists('Ksfraser\Event\EventManager')) {
        try {
            \Ksfraser\Event\EventManager::dispatchEvent($eventName, $eventData);
        } catch (Exception $e) {
            error_log('ST Event Error: ' . $e->getMessage());
        }
    }
    if (function_exists('add_hook')) {
        add_hook($eventName, function($data) use ($eventData) { return $eventData; });
    }
}

function add_ticket($ticket_data)
{
    $ticket_number = 'TKT-' . date('Ymd') . '-' . str_pad(time() % 10000, 4, '0', STR_PAD_LEFT);
    
    $sql = "INSERT INTO " . TB_PREF . "fa_st_tickets
        (ticket_number, subject, description, type, priority, status, debtor_no, contact_id,
         warranty_id, assigned_to, team_id, created_by)
    VALUES (" . db_escape($ticket_number) . ",
            " . db_escape($ticket_data['subject']) . ",
            " . db_escape($ticket_data['description']) . ",
            " . db_escape($ticket_data['type']) . ",
            " . db_escape($ticket_data['priority']) . ",
            " . db_escape($ticket_data['status']) . ",
            " . db_escape($ticket_data['debtor_no']) . ",
            " . db_escape($ticket_data['contact_id']) . ",
            " . db_escape($ticket_data['warranty_id']) . ",
            " . db_escape($ticket_data['assigned_to']) . ",
            " . db_escape($ticket_data['team_id']) . ",
            " . db_escape($ticket_data['created_by']) . ")";

    db_query($sql, "Could not add ticket");
    $ticket_id = db_insert_id();
    
    st_dispatch_event('ticket.created', ['ticket_id' => $ticket_id, 'data' => $ticket_data]);
    st_dispatch_event('db_postinsert', ['table' => 'fa_st_tickets', 'id' => $ticket_id]);
    
    return $ticket_id;
}

function get_ticket($ticket_id)
{
    $sql = "SELECT t.*, d.name as customer_name
        FROM " . TB_PREF . "fa_st_tickets t
        LEFT JOIN " . TB_PREF . "debtors_master d ON t.debtor_no = d.debtor_no
        WHERE t.id = " . db_escape($ticket_id);
    $result = db_query($sql, "Could not get ticket");
    return db_fetch_assoc($result);
}

function update_ticket($ticket_id, $ticket_data)
{
    $sql = "UPDATE " . TB_PREF . "fa_st_tickets SET
        subject = " . db_escape($ticket_data['subject']) . ",
        description = " . db_escape($ticket_data['description']) . ",
        type = " . db_escape($ticket_data['type']) . ",
        priority = " . db_escape($ticket_data['priority']) . ",
        status = " . db_escape($ticket_data['status']) . ",
        debtor_no = " . db_escape($ticket_data['debtor_no']) . ",
        contact_id = " . db_escape($ticket_data['contact_id']) . ",
        warranty_id = " . db_escape($ticket_data['warranty_id']) . ",
        assigned_to = " . db_escape($ticket_data['assigned_to']) . ",
        team_id = " . db_escape($ticket_data['team_id']) . "
    WHERE id = " . db_escape($ticket_id);

    db_query($sql, "Could not update ticket");
    
    st_dispatch_event('ticket.updated', ['ticket_id' => $ticket_id, 'data' => $ticket_data]);
    st_dispatch_event('db_postupdate', ['table' => 'fa_st_tickets', 'id' => $ticket_id]);
}

function delete_ticket($ticket_id)
{
    $old = get_ticket($ticket_id);
    $sql = "DELETE FROM " . TB_PREF . "fa_st_tickets WHERE id = " . db_escape($ticket_id);
    db_query($sql, "Could not delete ticket");
    
    st_dispatch_event('ticket.deleted', ['ticket_id' => $ticket_id, 'old_data' => $old]);
    st_dispatch_event('db_postdelete', ['table' => 'fa_st_tickets', 'id' => $ticket_id]);
}

function add_ticket_activity($ticket_id, $activity_data)
{
    $sql = "INSERT INTO " . TB_PREF . "fa_st_tickets_activities
        (ticket_id, activity_type, direction, subject, message, email_from, email_to,
         phone_number, duration_minutes, assigned_to, scheduled_at, completed_at, status)
    VALUES (" . db_escape($ticket_id) . ",
            " . db_escape($activity_data['activity_type']) . ",
            " . db_escape($activity_data['direction']) . ",
            " . db_escape($activity_data['subject']) . ",
            " . db_escape($activity_data['message']) . ",
            " . db_escape($activity_data['email_from']) . ",
            " . db_escape($activity_data['email_to']) . ",
            " . db_escape($activity_data['phone_number']) . ",
            " . db_escape($activity_data['duration_minutes']) . ",
            " . db_escape($activity_data['assigned_to']) . ",
            " . db_escape($activity_data['scheduled_at']) . ",
            " . db_escape($activity_data['completed_at']) . ",
            " . db_escape($activity_data['status']) . ")";

    db_query($sql, "Could not add activity");
    return db_insert_id();
}

function get_ticket_activities($ticket_id)
{
    $sql = "SELECT * FROM " . TB_PREF . "fa_st_tickets_activities
        WHERE ticket_id = " . db_escape($ticket_id) . "
        ORDER BY created_at DESC";
    $result = db_query($sql, "Could not get activities");
    $activities = [];
    while ($row = db_fetch_assoc($result)) {
        $activities[] = $row;
    }
    return $activities;
}

function add_ticket_note($ticket_id, $note_data)
{
    $sql = "INSERT INTO " . TB_PREF . "fa_st_tickets_notes
        (ticket_id, note, note_type, created_by)
    VALUES (" . db_escape($ticket_id) . ",
            " . db_escape($note_data['note']) . ",
            " . db_escape($note_data['note_type']) . ",
            " . db_escape($note_data['created_by']) . ")";

    db_query($sql, "Could not add note");
    return db_insert_id();
}

function get_ticket_notes($ticket_id)
{
    $sql = "SELECT * FROM " . TB_PREF . "fa_st_tickets_notes
        WHERE ticket_id = " . db_escape($ticket_id) . "
        ORDER BY created_at DESC";
    $result = db_query($sql, "Could not get notes");
    $notes = [];
    while ($row = db_fetch_assoc($result)) {
        $notes[] = $row;
    }
    return $notes;
}

function add_ticket_item($ticket_id, $item_data)
{
    $line_total = ($item_data['quantity'] ?? 1) * ($item_data['unit_price'] ?? 0);
    $sql = "INSERT INTO " . TB_PREF . "fa_st_tickets_items
        (ticket_id, item_type, item_description, quantity, unit_price, unit, invoice_id)
    VALUES (" . db_escape($ticket_id) . ",
            " . db_escape($item_data['item_type']) . ",
            " . db_escape($item_data['item_description']) . ",
            " . db_escape($item_data['quantity']) . ",
            " . db_escape($item_data['unit_price']) . ",
            " . db_escape($item_data['unit']) . ",
            " . db_escape($item_data['invoice_id']) . ")";

    db_query($sql, "Could not add item");
    return db_insert_id();
}

function get_ticket_items($ticket_id)
{
    $sql = "SELECT * FROM " . TB_PREF . "fa_st_tickets_items
        WHERE ticket_id = " . db_escape($ticket_id);
    $result = db_query($sql, "Could not get items");
    $items = [];
    while ($row = db_fetch_assoc($result)) {
        $items[] = $row;
    }
    return $items;
}

function get_ticket_total($ticket_id): float
{
    $items = get_ticket_items($ticket_id);
    return array_sum(array_column($items, 'line_total') ?? [0]);
}