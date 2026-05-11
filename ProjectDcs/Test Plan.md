# Test Plan - FA_SupportTickets

## Overview

This document outlines the test strategy, test types, test cases, and acceptance criteria for the Support Tickets module.

---

## 1. Test Strategy

### 1.1 Test Objectives

- Verify all functional requirements are met
- Ensure data integrity and consistency
- Validate integration with FA core and other modules
- Confirm security controls work correctly
- Achieve code quality standards

### 1.2 Test Levels

| Level | Description | Coverage Target |
|-------|-------------|-----------------|
| Unit Testing | Individual function/method testing | Core business logic |
| Integration Testing | Module integration with FA and other modules | All integrations |
| System Testing | End-to-end workflows | Critical paths |
| User Acceptance Testing | Business user validation | All use cases |

### 1.3 Test Types

| Type | Description |
|------|-------------|
| Functional Testing | Feature verification |
| Regression Testing | Existing functionality verification |
| Security Testing | Permission and access control |
| Performance Testing | Response times |
| UI/UX Testing | User interface validation |

---

## 2. Test Environment

### 2.1 Environment Requirements

- FrontAccounting 2.4.0+ installed
- PHP 8.0+
- MySQL 5.7+
- Web browser (Chrome/Firefox/Edge)
- FA_CRM module installed

### 2.2 Test Data

**Required Test Data**:
- At least 3 sample customers (debtors)
- At least 2 sample support teams
- At least 5 sample tickets (different statuses, priorities)
- At least 3 sample users with different permissions

---

## 3. Test Cases

### 3.1 Ticket Management Tests

#### TC-ST-001: Create New Ticket

| Field | Value |
|-------|-------|
| Test ID | TC-ST-001 |
| Description | Create a new support ticket with all required fields |
| Preconditions | User has ST_MANAGE_TICKET permission, customer exists |
| Steps | 1. Navigate to Support > All Tickets |
| | 2. Click "New Support Ticket" |
| | 3. Fill required fields (subject, description, type, priority) |
| | 4. Select customer from dropdown |
| | 5. Click "Create Ticket" |
| Expected Result | Ticket saved to database, appears in list with auto-generated ticket number |
| Pass Criteria | Ticket visible in list with correct data, ticket number in format TKT-YYYYMMDD-XXXX |

#### TC-ST-002: View Ticket List

| Field | Value |
|-------|-------|
| Test ID | TC-ST-002 |
| Description | View list of all support tickets |
| Preconditions | User has ST_VIEW_TICKET permission, tickets exist |
| Steps | 1. Navigate to Support > All Tickets |
| | 2. View displayed list |
| Expected Result | Tickets displayed in table format with all columns |
| Pass Criteria | All columns display correctly (ID, Ticket#, Subject, Type, Status, Priority, Account, Created) |

#### TC-ST-003: Edit Ticket

| Field | Value |
|-------|-------|
| Test ID | TC-ST-003 |
| Description | Modify existing ticket |
| Preconditions | Ticket exists |
| Steps | 1. Navigate to Support > All Tickets |
| | 2. Click Edit on ticket |
| | 3. Modify fields (subject, priority, status) |
| | 4. Click "Update Ticket" |
| Expected Result | Ticket updated |
| Pass Criteria | Changes reflected in ticket list |

#### TC-ST-004: Delete Ticket

| Field | Value |
|-------|-------|
| Test ID | TC-ST-004 |
| Description | Delete a ticket |
| Preconditions | Test ticket exists |
| Steps | 1. Navigate to ticket edit |
| | 2. Click Delete |
| | 3. Confirm deletion |
| Expected Result | Ticket removed from database |
| Pass Criteria | Ticket no longer in list |

#### TC-ST-005: View Ticket Details

| Field | Value |
|-------|-------|
| Test ID | TC-ST-005 |
| Description | View full ticket details |
| Preconditions | Ticket exists |
| Steps | 1. Navigate to Support > All Tickets |
| | 2. Click on ticket |
| Expected Result | Ticket detail page displays all information |
| Pass Criteria | All ticket fields displayed, customer name shown |

#### TC-ST-006: Ticket Validation - Empty Subject

| Field | Value |
|-------|-------|
| Test ID | TC-ST-006 |
| Description | Validation prevents creating ticket with empty subject |
| Preconditions | User has ST_MANAGE_TICKET permission |
| Steps | 1. Navigate to create ticket form |
| | 2. Leave subject empty |
| | 3. Attempt to submit |
| Expected Result | Error message displayed |
| Pass Criteria | "Subject cannot be empty" error shown |

---

### 3.2 Ticket Activities Tests

#### TC-ST-010: Add Activity to Ticket

| Field | Value |
|-------|-------|
| Test ID | TC-ST-010 |
| Description | Add an activity (call/email/meeting) to a ticket |
| Preconditions | Ticket exists |
| Steps | 1. Open ticket detail |
| | 2. Navigate to Ticket Activities section |
| | 3. Fill activity form |
| | 4. Click "Add Activity" |
| Expected Result | Activity saved and displayed in list |
| Pass Criteria | Activity appears in activities table |

#### TC-ST-011: View Activity History

| Field | Value |
|-------|-------|
| Test ID | TC-ST-011 |
| Description | View activity history for a ticket |
| Preconditions | Ticket has activities |
| Steps | 1. Open ticket detail |
| | 2. View Activities section |
| Expected Result | Activities displayed in chronological order |
| Pass Criteria | All activities shown with correct data |

---

### 3.3 Ticket Notes Tests

#### TC-ST-015: Add Note to Ticket

| Field | Value |
|-------|-------|
| Test ID | TC-ST-015 |
| Description | Add a note to a ticket |
| Preconditions | Ticket exists |
| Steps | 1. Open ticket detail |
| | 2. Navigate to Ticket Notes section |
| | 3. Select note type |
| | 4. Enter note text |
| | 5. Click "Add Note" |
| Expected Result | Note saved and displayed |
| Pass Criteria | Note appears in notes table |

#### TC-ST-016: View Notes

| Field | Value |
|-------|-------|
| Test ID | TC-ST-016 |
| Description | View all notes for a ticket |
| Preconditions | Ticket has notes |
| Steps | 1. Open ticket detail |
| | 2. View Notes section |
| Expected Result | Notes displayed |
| Pass Criteria | All notes shown with type and creator |

---

### 3.4 Ticket Items Tests

#### TC-ST-020: Add Service Item

| Field | Value |
|-------|-------|
| Test ID | TC-ST-020 |
| Description | Add a service item to a ticket |
| Preconditions | Ticket exists |
| Steps | 1. Open ticket detail |
| | 2. Add item with type, description, quantity, price |
| | 3. Save |
| Expected Result | Item saved |
| Pass Criteria | Item appears in items list, total calculated |

#### TC-ST-021: Calculate Ticket Total

| Field | Value |
|-------|-------|
| Test ID | TC-ST-021 |
| Description | Verify ticket total calculation |
| Preconditions | Ticket has multiple items |
| Steps | 1. View ticket items |
| | 2. Verify total |
| Expected Result | Total = sum(quantity * unit_price) for all items |
| Pass Criteria | Total correct |

---

### 3.5 Team Management Tests

#### TC-ST-025: Create Support Team

| Field | Value |
|-------|-------|
| Test ID | TC-ST-025 |
| Description | Create a new support team |
| Preconditions | User has ST_ADMIN permission |
| Steps | 1. Navigate to Support > Teams |
| | 2. Click "New Team" |
| | 3. Enter team name |
| | 4. Save |
| Expected Result | Team created |
| Pass Criteria | Team appears in teams list |

#### TC-ST-026: View Teams

| Field | Value |
|-------|-------|
| Test ID | TC-ST-026 |
| Description | View list of support teams |
| Preconditions | Teams exist |
| Steps | 1. Navigate to Support > Teams |
| Expected Result | Teams displayed |
| Pass Criteria | All teams shown |

---

### 3.6 Security Tests

#### TC-ST-030: Permission - View Tickets

| Field | Value |
|-------|-------|
| Test ID | TC-ST-030 |
| Description | User without permission cannot view tickets |
| Preconditions | User lacks ST_VIEW_TICKET |
| Steps | 1. User attempts to access Support > All Tickets |
| Expected Result | Access denied |
| Pass Criteria | Error message or redirect |

#### TC-ST-031: Permission - Manage Tickets

| Field | Value |
|-------|-------|
| Test ID | TC-ST-031 |
| Description | User without permission cannot create tickets |
| Preconditions | User lacks ST_MANAGE_TICKET |
| Steps | 1. User attempts to create ticket |
| Expected Result | Access denied |
| Pass Criteria | Error message displayed |

#### TC-ST-032: Permission - Admin Teams

| Field | Value |
|-------|-------|
| Test ID | TC-ST-032 |
| Description | User without ST_ADMIN cannot manage teams |
| Preconditions | User lacks ST_ADMIN |
| Steps | 1. User attempts to access Teams page |
| Expected Result | Access denied |
| Pass Criteria | Error message displayed |

---

### 3.7 Integration Tests

#### TC-ST-040: CRM Integration - Customer Link

| Field | Value |
|-------|-------|
| Test ID | TC-ST-040 |
| Description | Ticket linked to FA CRM customer |
| Preconditions | Customer exists in debtors_master |
| Steps | 1. Create ticket with customer |
| | 2. View ticket |
| Expected Result | Customer name displayed |
| Pass Criteria | Customer name shows in ticket detail |

#### TC-ST-041: CRM Integration - Customer Dropdown

| Field | Value |
|-------|-------|
| Test ID | TC-ST-041 |
| Description | Customer dropdown populated from FA CRM |
| Preconditions | Customers exist |
| Steps | 1. Navigate to ticket create form |
| | 2. View Account dropdown |
| Expected Result | Customers from debtors_master displayed |
| Pass Criteria | All active customers in dropdown |

#### TC-ST-042: Warranty Integration

| Field | Value |
|-------|-------|
| Test ID | TC-ST-042 |
| Description | Ticket linked to warranty |
| Preconditions | WarrantyManagement module installed |
| Steps | 1. Create ticket with warranty_id |
| | 2. Verify warranty status updated |
| Expected Result | Warranty status changed to "Claimed" |
| Pass Criteria | Warranty record shows Claimed status |

#### TC-ST-043: Event Dispatch - Ticket Created

| Field | Value |
|-------|-------|
| Test ID | TC-ST-043 |
| Description | ticket.created event dispatched |
| Preconditions | Event system available |
| Steps | 1. Create ticket |
| | 2. Check event fired |
| Expected Result | Event dispatched with ticket data |
| Pass Criteria | Event logged or listener triggered |

---

### 3.8 Ticket Type Tests

#### TC-ST-050: Ticket Types Configured

| Field | Value |
|-------|-------|
| Test ID | TC-ST-050 |
| Description | Default ticket types available |
| Preconditions | Module installed |
| Steps | 1. View ticket create form |
| | 2. Check Type dropdown |
| Expected Result | Question, Issue, Request, Bug options |
| Pass Criteria | All default types present |

#### TC-ST-051: Priority Levels

| Field | Value |
|-------|-------|
| Test ID | TC-ST-051 |
| Description | All priority levels available |
| Preconditions | Module installed |
| Steps | 1. View ticket create form |
| | 2. Check Priority dropdown |
| Expected Result | Low, Medium, High, Critical options |
| Pass Criteria | All priority levels present |

#### TC-ST-052: Status Values

| Field | Value |
|-------|-------|
| Test ID | TC-ST-052 |
| Description | All status values available |
| Preconditions | Module installed |
| Steps | 1. View ticket create form |
| | 2. Check Status dropdown |
| Expected Result | New, InProgress, Waiting, Resolved, Closed |
| Pass Criteria | All statuses present |

---

### 3.9 Form UI Tests

#### TC-ST-060: Form Labels

| Field | Value |
|-------|-------|
| Test ID | TC-ST-060 |
| Description | All form fields have labels |
| Preconditions | None |
| Steps | 1. View ticket form |
| | 2. Check all fields have labels |
| Expected Result | All fields labeled |
| Pass Criteria | No unlabeled fields |

#### TC-ST-061: Form Validation Messages

| Field | Value |
|-------|-------|
| Test ID | TC-ST-061 |
| Description | Validation errors displayed inline |
| Preconditions | None |
| Steps | 1. Submit invalid form |
| | 2. View error message |
| Expected Result | Error message near relevant field |
| Pass Criteria | Clear error message displayed |

---

## 4. Test Execution

### 4.1 Execution Order

1. Unit tests
2. Integration tests
3. System tests
4. UAT

### 4.2 Test Results Template

| Test ID | Test Name | Status | Notes |
|---------|-----------|--------|-------|
| TC-ST-001 | Create New Ticket | PASS/FAIL | |
| TC-ST-002 | View Ticket List | PASS/FAIL | |

### 4.3 Defect Reporting

| Field | Description |
|-------|-------------|
| Defect ID | Unique identifier (e.g., DEF-001) |
| Test ID | Related test case |
| Severity | Critical/Major/Minor |
| Description | Detailed description |
| Steps to Reproduce | Reproduction steps |
| Expected Result | What should happen |
| Actual Result | What actually happened |

---

## 5. Acceptance Criteria

### 5.1 Functional Acceptance

| Requirement ID | Description | Test Coverage |
|----------------|-------------|---------------|
| FR-1.1.1 | Create Ticket | TC-ST-001 |
| FR-1.2.1 | View Tickets | TC-ST-002 |
| FR-1.3.1 | Edit Ticket | TC-ST-003 |
| FR-1.4.1 | Delete Ticket | TC-ST-004 |
| FR-2.1.1 | Add Activity | TC-ST-010 |
| FR-3.1.1 | Add Note | TC-ST-015 |
| FR-4.1.1 | Add Item | TC-ST-020 |
| FR-5.1.1 | Create Team | TC-ST-025 |
| FR-7.1.1 | CRM Integration | TC-ST-040 |
| FR-7.2.1 | Warranty Integration | TC-ST-042 |
| FR-8.1.1 | Permission Enforcement | TC-ST-030, TC-ST-031, TC-ST-032 |

### 5.2 Non-Functional Acceptance

| Criteria | Target |
|----------|--------|
| Page Load Time | < 3 seconds |
| Database Queries | < 10 per page |
| Browser Compatibility | Chrome, Firefox, Edge |
| Access Control | All permissions enforced |
| Data Validation | All inputs validated |

---

## 6. Test Deliverables

| Deliverable | Description |
|-------------|-------------|
| Test Cases | This document |
| Test Data | Sample data for testing |
| Test Results | Execution results log |
| Defect Log | Issues found during testing |
| Test Summary | Final pass/fail report |

---

## 7. Test Schedule

| Phase | Duration | Activities |
|-------|----------|-----------|
| Unit Testing | 1 day | Function testing |
| Integration Testing | 2 days | Module integrations |
| System Testing | 2 days | End-to-end workflows |
| UAT | 3 days | User acceptance |
| Bug Fixing | Ongoing | Fix and retest |

---

## 8. Risk Management

### 8.1 Test Risks

| Risk | Mitigation |
|------|-------------|
| Test data not available | Create sample data first |
| Environment issues | Use isolated test environment |
| Scope creep | Track changes to requirements |
| Module dependencies | Test with and without optional modules |
