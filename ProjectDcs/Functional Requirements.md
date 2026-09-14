# Functional Requirements - FA_SupportTickets

## Overview

This document defines the detailed functional requirements for the Support Tickets module, including all features, user interactions, data handling, and business rules.

---

## 1. Ticket Management

### 1.1 Create Ticket

**FR-1.1.1** The system SHALL allow users with ST_MANAGE_TICKET permission to create new support tickets.

**FR-1.1.2** The system SHALL require a subject field (minimum 1 character) for ticket creation.

**FR-1.1.3** The system SHALL auto-generate a unique ticket number in format TKT-YYYYMMDD-XXXX.

**FR-1.1.4** The system SHALL support the following ticket types:
- Question
- Issue
- Request
- Bug

**FR-1.1.5** The system SHALL support the following priority levels:
- Low
- Medium
- High
- Critical

**FR-1.1.6** The system SHALL support the following default statuses:
- New
- InProgress
- Waiting
- Resolved
- Closed

**FR-1.1.7** The system SHALL allow optional linking to a customer (debtor).

**FR-1.1.8** The system SHALL allow optional linking to a contact.

**FR-1.1.9** The system SHALL allow optional linking to a warranty.

**FR-1.1.10** The system SHALL allow optional assignment to a team member.

**FR-1.1.11** The system SHALL allow optional assignment to a team.

**FR-1.1.12** The system SHALL automatically record the creating user and timestamp.

### 1.2 View Tickets

**FR-1.2.1** The system SHALL display all tickets in a table format with columns:
- ID
- Ticket Number
- Subject
- Type
- Status
- Priority
- Customer
- Created Date

**FR-1.2.2** The system SHALL allow users with ST_VIEW_TICKET permission to view tickets.

**FR-1.2.3** The system SHALL display customer name by joining with debtors_master table.

**FR-1.2.4** The system SHALL sort tickets by created_at descending by default.

### 1.3 Edit Ticket

**FR-1.3.1** The system SHALL allow users with ST_MANAGE_TICKET permission to edit tickets.

**FR-1.3.2** The system SHALL allow editing of all mutable fields:
- Subject
- Description
- Type
- Priority
- Status
- Assigned To
- Resolution

**FR-1.3.3** The system SHALL validate subject is not empty on update.

**FR-1.3.4** The system SHALL dispatch ticket.updated event on save.

### 1.4 Delete Ticket

**FR-1.4.1** The system SHALL allow deletion of tickets by authorized users.

**FR-1.4.2** The system SHALL delete associated activities, notes, and items (via cascade).

**FR-1.4.3** The system SHALL dispatch ticket.deleted event on delete.

### 1.5 Ticket Status Workflow

**FR-1.5.1** The system SHALL allow status transitions in any order.

**FR-1.5.2** The system SHALL track status history via ticket activities.

---

## 2. Ticket Activities

### 2.1 Activity Types

**FR-2.1.1** The system SHALL support the following activity types:
- Call (inbound/outbound)
- Email (sent/received)
- Meeting
- Note
- Other

**FR-2.1.2** The system SHALL allow scheduling future activities.

**FR-2.1.3** The system SHALL allow logging activity duration.

### 2.2 Activity Management

**FR-2.2.1** The system SHALL allow adding activities to any ticket.

**FR-2.2.2** The system SHALL display activities in chronological order (newest first).

**FR-2.2.3** The system SHALL display the following activity fields:
- Date
- Action Type
- Direction
- Subject
- Message
- Notes
- Performed By

---

## 3. Ticket Notes

### 3.1 Note Types

**FR-3.1.1** The system SHALL support the following note types:
- Comment
- Internal
- Public

### 3.2 Note Management

**FR-3.2.1** The system SHALL allow adding notes to any ticket.

**FR-3.2.2** The system SHALL display notes in chronological order.

**FR-3.2.3** The system SHALL record the note creator and timestamp.

---

## 4. Ticket Items

### 4.1 Item Types

**FR-4.1.1** The system SHALL support tracking service items on tickets.

**FR-4.1.2** The system SHALL allow the following item types:
- Service
- Parts
- Labor
- Travel

### 4.2 Item Fields

**FR-4.2.1** Each item SHALL have:
- Item Type
- Description
- Quantity
- Unit Price
- Unit
- Invoice ID (optional)

**FR-4.2.2** The system SHALL calculate line total (quantity * unit_price).

### 4.3 Item Total

**FR-4.3.1** The system SHALL calculate total of all items on a ticket.

**FR-4.3.2** The system SHALL allow items to be linked to invoices.

---

## 5. Team Management

### 5.1 Teams

**FR-5.1.1** The system SHALL allow creating support teams.

**FR-5.1.2** The system SHALL allow editing team name and leader.

**FR-5.1.3** The system SHALL allow deactivating teams.

### 5.2 Permissions

**FR-5.2.1** Only users with ST_ADMIN permission SHALL manage teams.

---

## 6. Ticket Types Configuration

### 6.1 Type Management

**FR-6.1.1** The system SHALL provide default ticket types:
- Question
- Issue
- Request
- Bug

**FR-6.1.2** The system SHALL allow configuring if a type requires a project.

### 6.2 Initial Data

**FR-6.2.1** The system SHALL insert default ticket types on first installation:
| Name | Description | Requires Project |
|------|-------------|-----------------|
| Question | Customer question about product/service | No |
| Issue | Product or service issue | Yes |
| Request | Service request | No |
| Bug | Bug report | Yes |

---

## 7. Integration

### 7.1 FA CRM Integration

**FR-7.1.1** The system SHALL link tickets to customers from debtors_master.

**FR-7.1.2** The system SHALL display customer name in ticket list.

### 7.2 Warranty Integration

**FR-7.2.1** The system SHALL link tickets to warranties when warranty_id provided.

**FR-7.2.2** Creating a ticket with warranty SHALL update warranty status to "Claimed".

**FR-7.2.3** When warranty.expired event received, system SHALL alert on linked tickets.

### 7.3 Event Integration

**FR-7.3.1** The system SHALL dispatch ticket.created event.

**FR-7.3.2** The system SHALL dispatch ticket.updated event.

**FR-7.3.3** The system SHALL dispatch ticket.deleted event.

**FR-7.3.4** The system SHALL listen to warranty.claimed event.

**FR-7.3.5** The system SHALL listen to warranty.resolution_started event.

**FR-7.3.6** The system SHALL listen to warranty.expired event.

**FR-7.3.7** The system SHALL listen to rma.created event.

---

## 8. Security

### 8.1 Permission Model

**FR-8.1.1** The system SHALL enforce ST_VIEW_TICKET for viewing tickets.

**FR-8.1.2** The system SHALL enforce ST_MANAGE_TICKET for creating/editing tickets.

**FR-8.1.3** The system SHALL enforce ST_ADMIN for team management.

### 8.2 Data Validation

**FR-8.2.1** The system SHALL validate subject is not empty.

**FR-8.2.2** The system SHALL use db_escape() to prevent SQL injection.

---

## 9. User Interface

### 9.1 Menu

**FR-9.1.1** The system SHALL provide Support menu group.

**FR-9.1.2** The menu SHALL include All Tickets (ST_VIEW_TICKET).

**FR-9.1.3** The menu SHALL include My Tickets (ST_VIEW_TICKET).

**FR-9.1.4** The menu SHALL include Teams (ST_ADMIN).

### 9.2 Forms

**FR-9.2.1** New ticket form SHALL include all required and optional fields.

**FR-9.2.2** Ticket edit form SHALL pre-populate existing values.

**FR-9.2.3** The system SHALL display validation errors inline.

---

## 10. Reports and Analytics

### 10.1 Dashboard

**FR-10.1.1** The system SHALL provide ticket count by status.

**FR-10.1.2** The system SHALL provide ticket count by priority.

---

## 11. Non-Functional Requirements

### 11.1 Performance

**FR-11.1.1** Ticket list SHALL load in under 3 seconds.

**FR-11.1.2** Database queries SHALL use proper indexing.

### 11.2 Compatibility

**FR-11.2.1** The module SHALL be compatible with FrontAccounting 2.4+.

**FR-11.2.2** The module SHALL support PHP 8.0+.

### 11.3 Extensibility

**FR-11.3.1** Additional ticket types SHALL be configurable.

**FR-11.3.2** Additional activity types SHALL be supported via configuration.

---
