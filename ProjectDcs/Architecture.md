# Architecture - FA_SupportTickets

## Overview

This document describes the technical architecture for the Support Tickets module, including the layered architecture, component design, database schema, and integration patterns.

---

## 1. System Architecture

### 1.1 High-Level Architecture

```
┌─────────────────────────────────────────────────────────────────────┐
│                    Presentation Layer                               │
│  ┌─────────────┐ ┌──────────────┐ ┌──────────────┐              │
│  │  Tickets    │ │Ticket Detail │ │    Teams    │              │
│  │    Page    │ │    Page     │ │    Page     │              │
│  └──────┬──────┘ └──────┬─────┘ └──────┬─────┘              │
│         │               │              │                         │
├─────────┼──────────────┼──────────────┼───────────────────┤
│                    Service Layer                               │
│  ┌──────────────────────────────────────────────────────┐  │
│  │                st_db.inc                            │  │
│  │   Database CRUD functions (add, get, update, delete)  │  │
│  └──────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────┐  │
│  │            Event Functions                          │  │
│  │   st_dispatch_event, event wiring               │  │
│  └──────────────────────────────────────────────────────┘  │
├──────────────────────────────────────────────────────────┤
│                    Data Layer                                │
│  ┌──────────┐ ┌──────────────┐ ┌──────────┐              │
│  │ Tickets  │ │  Activities  │ │  Notes  │              │
│  │  Table   │ │    Table     │ │  Table  │              │
│  └──────────┘ └──────────────┘ └──────────┘              │
├──────────────────────────────────────────────────────────┤
│                  Integration Layer                          │
│  ┌──────────┐ ┌──────────────┐ ┌──────────┐ ┌────────┐  │
│  │FA CRM     │ │Warranty     │ │Project  │ │Events  │  │
│  │(Debtors)  │ │Management    │ │  Mgmt   │ │PSR-14  │  │
│  └──────────┘ └─────────────���┘ └──────────┘ └────────┘  │
└─────────────────────────────────────────────────────────────┘
```

### 1.2 Module Structure

```
ksf_FA_SupportTickets/
├── FA_St_Module.php          # Module class with permissions
├── hooks.php                 # FA lifecycle hooks and table creation
├── composer.json            # Composer dependencies
├── includes/
│   ├── st_db.inc          # Database functions
│   └── st_event_wiring.php # Cross-module event wiring
├── pages/
│   ├── tickets.php        # Ticket list and create
│   ├── ticket_detail.php # Ticket detail/edit
│   ├── my_tickets.php   # User's ticket view
│   └── teams.php        # Team management
├── sql/
│   ├── install.sql      # Schema creation (reference)
│   └── uninstall.sql    # Schema removal
├── _init/
│   └── init.inc        # Module initialization
└── ProjectDcs/          # Documentation
```

---

## 2. Component Design

### 2.1 Core Components

#### FA_St_Module
Main module class handling initialization and hook registration.

**Responsibilities**:
- Module initialization
- Permission definition
- Menu item registration

```php
class FA_St_Module
{
    public function __construct()
    public function on_fa_init(): void
    public function display_ticket_extra_fields(string $ticketId): void
}
```

#### st_db.inc Functions
Procedural database operations for CRUD.

**Ticket Functions**:
- `add_ticket($ticket_data)` - Create ticket
- `get_ticket($ticket_id)` - Fetch single ticket
- `update_ticket($ticket_id, $ticket_data)` - Update ticket
- `delete_ticket($ticket_id)` - Delete ticket

**Activity Functions**:
- `add_ticket_activity($ticket_id, $activity_data)` - Add activity
- `get_ticket_activities($ticket_id)` - List activities

**Note Functions**:
- `add_ticket_note($ticket_id, $note_data)` - Add note
- `get_ticket_notes($ticket_id)` - List notes

**Item Functions**:
- `add_ticket_item($ticket_id, $item_data)` - Add item
- `get_ticket_items($ticket_id)` - List items
- `get_ticket_total($ticket_id)` - Calculate total

#### Event Handling

**st_dispatch_event()**:
Dispatches events to PSR-14 EventManager and FA hooks.

```php
function st_dispatch_event(string $eventName, $eventData): void
```

**st_event_wiring.php**:
Cross-module event listener registrations.

```php
EventManager::listen('ticket.created', function($event) { ... })
EventManager::listen('warranty.claimed', function($event) { ... })
```

### 2.2 Database Functions (st_db.inc)

#### Ticket Functions
- `add_ticket($ticket_data)` - Create new ticket with auto-generated ticket number
- `get_ticket($ticket_id)` - Get ticket with customer join
- `update_ticket($ticket_id, $ticket_data)` - Update ticket fields
- `delete_ticket($ticket_id)` - Delete ticket and cascade

#### Activity Functions
- `add_ticket_activity($ticket_id, $activity_data)` - Log activity
- `get_ticket_activities($ticket_id)` - Get activity history

#### Note Functions
- `add_ticket_note($ticket_id, $note_data)` - Add note
- `get_ticket_notes($ticket_id)` - Get notes

#### Item Functions
- `add_ticket_item($ticket_id, $item_data)` - Add service item
- `get_ticket_items($ticket_id)` - Get items
- `get_ticket_total($ticket_id)` - Calculate total cost

---

## 3. Database Schema

### 3.1 Entity Relationship Diagram

```
┌──────────────────┐         ┌──────────────────┐
│  debtors_master  │         │    employees     │
│     (FA CRM)     │         │    (FA HRM)     │
└────────┬────────���┘         └────────┬─────────┘
         │                            │
         │ 1:N                       │ N:1
         ▼                          ▼
┌────────────────────────────────────────────────────────┐
│              fa_st_tickets                             │
│ ┌──────────────────────────────────────────────────┐ │
│ │ id (PK)                                           │ │
│ │ ticket_number (UK)                                │ │
│ │ subject                                          │ │
│ │ description                                      │ │
│ │ type, status, priority                           │ │
│ │ debtor_no (FK) ──────────► debtors_master        │ │
│ │ contact_id (FK) ──────────► contacts           │ │
│ │ warranty_id (FK) ────────► fa_wm_liability   │ │
│ │ assigned_to (FK) ────────► employees           │ │
│ │ team_id (FK) ────────────► fa_st_teams        │ │
│ │ project_id (FK) ────────► fa_pm_projects      │ │
│ │ invoice_id (FK) ────────► fa_debtor_trans     │ │
│ │ created_by, created_at, updated_at             │ │
│ └──────────────────────────────────────────────┘ │
└───────────────────────────┬─────────────────────────┘
                          │ 1:N
                          ▼
┌──────────────────────────┬─────────────────────────┐
│    fa_st_tickets_activities              │         │
│ ┌────────────────────────────────────┐──────────┤
│ │ id (PK)                            │          │
│ │ ticket_id (FK) ──────► fa_st_tickets │          │
│ │ activity_type, direction          │          │
│ │ subject, message                │          │
│ │ email_from, email_to            │          │
│ │ phone_number, duration_minutes  │          │
│ │ assigned_to                   │          │
│ │ scheduled_at, completed_at  │          │
│ │ status                      │          │
│ │ created_at                  │          │
│ └─────────────────────────────┘          │
└────────────────────────────────────────┘

┌──────────────────────────┬─────────────────────────┐
│    fa_st_tickets_notes               │         │
│ ┌────────────────────────────────────┐──────────┤
│ │ id (PK)                            │          │
│ │ ticket_id (FK) ──────► fa_st_tickets│          │
│ │ note                              │          │
│ │ note_type                        │          │
│ │ created_by, created_at          │          │
│ └─────────────────────────────┘          │
└────────────��─��─────────────────────────┘

┌──────────────────────────┬─────────────────────────┐
│    fa_st_tickets_items               │         │
│ ┌────────────────────────────────────┐──────────┤
│ │ id (PK)                            │          │
│ │ ticket_id (FK) ──────► fa_st_tickets │          │
│ │ item_type, item_description        │          │
│ │ quantity, unit_price, unit       │          │
│ │ invoice_id (FK)                 │          │
│ │ created_at                      │          │
│ └─────────────────────────────┘          │
└────────────────────────────────────────┘
```

### 3.2 Table Details

#### fa_st_tickets
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

## 4. Integration Patterns

### 4.1 FA Integration

The module integrates with FrontAccounting core:

#### Database Integration
- Uses FA's `db_query()`, `db_fetch_assoc()` functions
- Uses `TB_PREF` for table prefix
- Uses `TB_PREF . "debtors_master"` for customers (CRM)

#### Session Integration
- Uses `$page_security` for permission checks
- Defines permissions in `FA_St_Module.php`
- Uses `$_SESSION['wa_current_user']->name` for created_by

#### UI Integration
- Uses FA's `page()`, `start_table()`, `end_table()`
- Uses FA's form helpers (text_row_ex, textarea_row, select_row)

### 4.2 Module Integration

#### FA_CRM (Required)
- Links tickets to `debtors_master` table
- Uses customer dropdown in ticket forms

#### WarrantyManagement (Optional)
- Links tickets to warranty claims
- Updates warranty status when ticket created
- Updates ticket when warranty claimed/resolved

#### ProjectManagement (Optional)
- Links tickets to projects
- Project selection in ticket forms

### 4.3 Event Integration

PSR-14 event dispatcher for decoupled operations:

**Dispatched Events**:
- `ticket.created` - On ticket creation
- `ticket.updated` - On ticket update
- `ticket.deleted` - On ticket deletion

**Listened Events**:
- `warranty.claimed` - Auto-update ticket status
- `warranty.resolution_started` - Track resolution
- `warranty.expired` - Alert on linked tickets
- `rma.created` - Update ticket from RMA

---

## 5. Security Architecture

### 5.1 Permission Model

Defined in FA_St_Module.php:

| Permission | Description |
|------------|-------------|
| ST_VIEW_TICKET | View ticket list and details |
| ST_MANAGE_TICKET | Create, edit, delete tickets |
| ST_ASSIGN_TICKET | Assign tickets to teams/members |
| ST_ADMIN | Administer teams and types |

### 5.2 Page Security

Each page starts with:
```php
$page_security = 'ST_VIEW_TICKET';
```

### 5.3 Data Validation

- SQL injection prevention via `db_escape()`
- Required field validation in business logic
- Input sanitization via htmlspecialchars() (FA handles)

---

## 6. Design Patterns

### 6.1 Patterns Used

| Pattern | Implementation |
|---------|----------------|
| Data Access Object | st_db.inc functions |
| Event Dispatcher | st_dispatch_event() + PSR-14 |
| Hook System | FA hooks.php integration |
| Module Pattern | FA_St_Module class |

### 6.2 Dependency Management

- Composer for dependency loading (ComposerDependencyManager)
- Conditional feature loading based on available classes

---

## 7. Configuration

### 7.1 Module Configuration

**Version**: 1.0.0

**Category**: CRM

**Dependencies**:
- FA_CRM (required)

### 7.2 Initial Data

Ticket types inserted on install:
| Name | Description | Requires Project |
|------|-------------|-----------------|
| Question | Customer question about product/service | 0 |
| Issue | Product or service issue | 1 |
| Request | Service request | 0 |
| Bug | Bug report | 1 |

Teams inserted on install:
| Name |
|------|
| Support Team |
| Technical Support |
| Billing |

---

## 8. Deployment

### 8.1 Installation

1. Copy module to `/modules/FA_SupportTickets/`
2. Activate via FA Modules admin
3. hooks.php creates database tables
4. Permissions created in FA security

### 8.2 Table Creation

`fa_st_create_tables()` in hooks.php:
- Creates all module tables
- Creates indexes for performance

### 8.3 Module Lifecycle Hooks

- `fa_st_install()` - Create tables, insert initial data
- `fa_st_activate()` - Register hooks
- `fa_st_deactivate()` - Cleanup
- `fa_st_uninstall()` - Drop tables

---

## 9. Events Reference

### 9.1 Event Flow - Ticket Created

```
User submits ticket form
        │
        ▼
st_db::add_ticket()
        │
        ├─► Insert to fa_st_tickets
        │
        ▼
st_dispatch_event('ticket.created')
        │
        ├─► PSR-14 EventManager::dispatchEvent
        │
        ├─► fa_st_on_ticket_created() hook
        │
        ▼
st_event_wiring::listener(ticket.created)
        │
        ├─► If warranty_id:
        │   Update warranty to "Claimed"
        │   Dispatch warranty.claimed
        │
        └─► Complete
```

### 9.2 Event Flow - Warranty Claim

```
WarrantyManagement creates claim
        │
        ▼
EventManager::dispatchEvent('warranty.claimed')
        │
        ▼
st_event_wiring::listener(warranty.claimed)
        │
        ▼
Update linked ticket: status='InProgress'
```

---

## 10. API Reference

### 10.1 Public Functions

```php
// Module info
fa_st_get_module_info(): array

// Menu items
fa_st_get_menu_items(): array

// Database CRUD
add_ticket(array $ticket_data): int
get_ticket(int $ticket_id): array
update_ticket(int $ticket_id, array $ticket_data): void
delete_ticket(int $ticket_id): void

// Activities
add_ticket_activity(int $ticket_id, array $activity_data): int
get_ticket_activities(int $ticket_id): array

// Notes
add_ticket_note(int $ticket_id, array $note_data): int
get_ticket_notes(int $ticket_id): array

// Items
add_ticket_item(int $ticket_id, array $item_data): int
get_ticket_items(int $ticket_id): array
get_ticket_total(int $ticket_id): float

// Events
st_dispatch_event(string $eventName, mixed $eventData): void
```

### 10.2 Permissions

```php
define('ST_VIEW_TICKET', 'ST_VIEW_TICKET');
define('ST_MANAGE_TICKET', 'ST_MANAGE_TICKET');
define('ST_ASSIGN_TICKET', 'ST_ASSIGN_TICKET');
define('ST_ADMIN', 'ST_ADMIN');
```
