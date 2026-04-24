<?php
/**
 * Cross-Module Event Wiring
 * Connects SupportTickets and WarrantyManagement via PSR-14 events
 */

if (!class_exists('Ksfraser\Event\EventManager')) {
    return;
}

use Ksfraser\Event\EventManager;

EventManager::listen('ticket.created', function($event) {
    $data = $event->getData();
    
    if (!empty($data['warranty_id'])) {
        $warrantyId = $data['warranty_id'];
        error_log("Warranty event wiring: Ticket created linked to warranty {$warrantyId}");
        
        global $db;
        $sql = "UPDATE " . TB_PREF . "fa_wm_liability SET 
            status = 'Claimed',
            current_value = 0
            WHERE id = " . db_escape($warrantyId);
        db_query($sql, "Could not update warranty liability");
        
        EventManager::dispatchEvent('warranty.claimed', [
            'warranty_id' => $warrantyId,
            'ticket_id' => $data['ticket_id'] ?? 0,
        ]);
    }
    
    return $event;
});

EventManager::listen('ticket.updated', function($event) {
    $data = $event->getData();
    
    if ($data['data']['status'] === 'Resolved' && !empty($data['ticket_id'])) {
        $ticketId = $data['ticket_id'];
        
        $sql = "SELECT warranty_id FROM " . TB_PREF . "fa_st_tickets WHERE id = " . db_escape($ticketId);
        $result = db_query($sql, "Could not get ticket");
        $ticket = db_fetch_assoc($result);
        
        if (!empty($ticket['warranty_id'])) {
            EventManager::dispatchEvent('warranty.resolution_started', [
                'warranty_id' => $ticket['warranty_id'],
                'ticket_id' => $ticketId,
            ]);
        }
    }
    
    return $event;
});

EventManager::listen('warranty.claimed', function($event) {
    $data = $event->getData();
    
    if (!empty($data['ticket_id'])) {
        $ticketId = $data['ticket_id'];
        
        global $db;
        $sql = "UPDATE " . TB_PREF . "fa_st_tickets SET 
            status = 'InProgress',
            resolution = 'Warranty claim in progress'
            WHERE id = " . db_escape($ticketId);
        db_query($sql, "Could not update ticket from warranty event");
    }
    
    return $event;
});

EventManager::listen('warranty.expired', function($event) {
    $data = $event->getData();
    
    if (!empty($data['warranty_id'])) {
        $warrantyId = $data['warranty_id'];
        
        global $db;
        $sql = "SELECT id FROM " . TB_PREF . "fa_st_tickets 
            WHERE warranty_id = " . db_escape($warrantyId) . "
            AND status NOT IN ('Closed', 'Resolved')";
        $result = db_query($sql, "Could not get linked tickets");
        
        while ($row = db_fetch_assoc($result)) {
            EventManager::dispatchEvent('ticket.warning', [
                'ticket_id' => $row['id'],
                'message' => 'Linked warranty has expired',
            ]);
        }
    }
    
    return $event;
});

EventManager::listen('rma.created', function($event) {
    $data = $event->getData();
    
    if (!empty($data['rma_id']) && !empty($data['ticket_id'])) {
        $rmaId = $data['rma_id'];
        $ticketId = $data['ticket_id'];
        
        global $db;
        $sql = "UPDATE " . TB_PREF . "fa_st_tickets SET 
            status = 'InProgress',
            resolution = 'RMA {$rmaId} in progress'
            WHERE id = " . db_escape($ticketId);
        db_query($sql, "Could not update ticket from RMA event");
    }
    
    return $event;
});

return true;