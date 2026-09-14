# FA_SupportTickets Module

Support Tickets (Cases) for FrontAccounting - A CRM module for managing customer support requests, warranty claims, issue tracking, and service tickets.

---

## Overview

The FA_SupportTickets module provides a complete helpdesk and support ticket management system integrated with FrontAccounting. It enables businesses to track customer inquiries, manage warranty claims, log issues and bugs, and assign tickets to support teams for resolution.

## Features

### Core Functionality
- **Ticket Management**: Create, view, update, and delete support tickets
- **Ticket Types**: Question, Issue, Request, Bug
- **Priority Levels**: Low, Medium, High, Critical
- **Status Tracking**: New, In Progress, Waiting, Resolved, Closed
- **Ticket Activities**: Log calls, emails, meetings, and other activities
- **Ticket Notes**: Add comments and internal notes to tickets
- **Ticket Items**: Track service items and costs associated with tickets

### Integration Features
- **Customer Integration**: Link tickets to FA CRM customers (debtors)
- **Warranty Integration**: Link tickets to warranty claims (WarrantyManagement module)
- **RMA Integration**: Link tickets to return merchandise authorizations
- **Project Integration**: Associate tickets with projects
- **Invoice Integration**: Link tickets to invoices for billing

### Team Management
- **Support Teams**: Create and manage support teams
- **Ticket Assignment**: Assign tickets to team members
- **Team Leaders**: Designate team leaders

### Event-Driven Architecture
- **PSR-14 Events**: Event-driven integration with other modules
- **Cross-Module Wiring**: Automatic workflow triggers between modules

---

## Quick Start

### Installation

1. Copy the module to your FrontAccounting modules directory:
   ```
   /modules/FA_SupportTickets/
   ```

2. Activate the module via FA Modules Administration

3. The module will automatically create database tables on activation

### Configuration

After installation, configure the following in the module:

1. **Create Support Teams**: Navigate to Support > Teams to create support teams
2. **Set Up Ticket Types**: Configure ticket types for your business needs
3. **Configure Permissions**: Assign appropriate permissions to user roles

### Usage

1. **Creating Tickets**:
   - Navigate to Support > All Tickets
   - Click "New Support Ticket"
   - Fill in the required fields (subject, description, type, priority)
   - Optionally link to customer, warranty, or project
   - Click "Create Ticket"

2. **Managing Tickets**:
   - View all tickets in the tickets list
   - Click on a ticket to view details
   - Add activities (calls, emails, meetings)
   - Add notes for communication
   - Update status as the ticket progresses

3. **Assigning Tickets**:
   - Select an assigned team member
   - Assign to a support team
   - Track resolution progress

---

## Database Tables

### Main Tables

#### fa_st_tickets
Main tickets table storing all support ticket data.

```sql
CREATE TABLE `@TB_PREF@fa_st_tickets` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### fa_st_tickets_activities
Activity log for tickets (calls, emails, meetings).

```sql
CREATE TABLE `@TB_PREF@fa_st_tickets_activities` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### fa_st_tickets_notes
Notes and comments on tickets.

```sql
CREATE TABLE `@TB_PREF@fa_st_tickets_notes` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `ticket_id` INT(11) NOT NULL,
    `note` TEXT NOT NULL,
    `note_type` VARCHAR(20) DEFAULT 'General',
    `created_by` VARCHAR(100) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ticket_id` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### fa_st_tickets_items
Service items and costs associated with tickets.

```sql
CREATE TABLE `@TB_PREF@fa_st_tickets_items` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### fa_st_teams
Support teams.

```sql
CREATE TABLE `@TB_PREF@fa_st_teams` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `leader_id` VARCHAR(100) DEFAULT NULL,
    `inactive` TINYINT(1) DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### fa_st_ticket_types
Ticket types configuration.

```sql
CREATE TABLE `@TB_PREF@fa_st_ticket_types` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(30) NOT NULL,
    `description` VARCHAR(100) DEFAULT NULL,
    `requires_project` TINYINT(1) DEFAULT 0,
    `inactive` TINYINT(1) DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## Permissions

The module defines the following permissions:

| Permission | Description | Menu Access |
|------------|-------------|-------------|
| ST_VIEW_TICKET | View tickets | All Tickets, My Tickets |
| ST_MANAGE_TICKET | Create/edit/update tickets | All Tickets |
| ST_ASSIGN_TICKET | Assign tickets to teams/members | All Tickets |
| ST_ADMIN | Administer teams and types | Teams |

### Default Menu Items

```
Support
├── All Tickets          (ST_VIEW_TICKET)
├── My Tickets          (ST_VIEW_TICKET)
└── Teams              (ST_ADMIN)
```

---

## API Reference

### Database Functions (st_db.inc)

#### Ticket Functions

- `add_ticket($ticket_data)` - Create a new ticket
- `get_ticket($ticket_id)` - Get a single ticket
- `update_ticket($ticket_id, $ticket_data)` - Update a ticket
- `delete_ticket($ticket_id)` - Delete a ticket
- `get_ticket_total($ticket_id)` - Calculate ticket total

#### Activity Functions

- `add_ticket_activity($ticket_id, $activity_data)` - Add an activity
- `get_ticket_activities($ticket_id)` - Get ticket activities

#### Note Functions

- `add_ticket_note($ticket_id, $note_data)` - Add a note
- `get_ticket_notes($ticket_id)` - Get ticket notes

#### Item Functions

- `add_ticket_item($ticket_id, $item_data)` - Add a service item
- `get_ticket_items($ticket_id)` - Get ticket items

### Event Functions

- `st_dispatch_event($eventName, $eventData)` - Dispatch an event

### Module Functions (FA_St_Module.php)

- `fa_st_get_module_info()` - Get module information
- `fa_st_get_menu_items()` - Get menu items

### Events (PSR-14)

#### Dispatched Events

- `ticket.created` - When a ticket is created
- `ticket.updated` - When a ticket is updated
- `ticket.deleted` - When a ticket is deleted

#### Listened Events

- `warranty.claimed` - Warranty claim processed
- `warranty.resolution_started` - Resolution started
- `warranty.expired` - Warranty expired
- `rma.created` - RMA created

---

## Module Structure

```
FA_SupportTickets/
├── FA_St_Module.php        # Module class and permissions
├── hooks.php              # FA lifecycle hooks
├── composer.json         # Composer dependencies
├── includes/
│   ├── st_db.inc       # Database functions
│   └── st_event_wiring.php  # Event wiring
├── pages/
│   ├── tickets.php        # Ticket list/create
│   ├── ticket_detail.php # Ticket detail/edit
│   ├── my_tickets.php    # My tickets view
│   └── teams.php         # Team management
├── sql/
│   ├── install.sql    # Schema creation
│   └── uninstall.sql # Schema removal
├── _init/
│   └── init.inc     # Module initialization
└── ProjectDcs/         # Documentation
```

---

## Integration with Other Modules

### FA_CRM (Required)

The module integrates with FA CRM for customer data:
- Links tickets to customers (debtors_master)
- Displays customer name in ticket details
- Customer selection dropdown in ticket forms

### WarrantyManagement (Optional)

When WarrantyManagement module is installed:
- Tickets can be linked to warranties
- Creating a ticket with warranty_id triggers warranty claim
- Warranty resolution updates ticket status automatically

### ProjectManagement (Optional)

When ProjectManagement module is installed:
- Tickets can be associated with projects
- Project information displayed in ticket details

### Employee Management

The module can integrate with FA Employee Management for:
- Employee assignment to tickets
- Team member management

---

## Support

For issues and questions:
- Check the ProjectDcs/ folder for detailed documentation
- Review the Functional Requirements for feature details
- Check the Test Plan for validation criteria

---

## Version History

| Version | Date | Changes |
|---------|-----|---------|
| 1.0.0 | 2024-04-23 | Initial release |

---

## License

This module is part of the KSFII FrontAccounting Extensions.
See LICENSE file for details.
