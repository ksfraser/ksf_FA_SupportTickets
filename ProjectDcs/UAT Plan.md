# UAT Plan - FA_SupportTickets

## Overview

This document defines the User Acceptance Test cases for the Support Tickets module. UAT validates that the module meets business requirements and is ready for production deployment.

---

## 1. UAT Objectives

### 1.1 Purpose

The purpose of UAT is to:
- Validate that the module meets business requirements
- Confirm the system works as expected from a user's perspective
- Ensure integration with other FA modules functions correctly
- Verify security and access controls are properly implemented

### 1.2 Success Criteria

UAT is considered successful when:
- All critical test cases pass
- All major test cases pass
- No critical defects remain open
- Business users sign off on functionality

---

## 2. UAT Environment

### 2.1 Setup Requirements

| Requirement | Specification |
|-------------|---------------|
| FrontAccounting Version | 2.4.0+ |
| PHP Version | 8.0+ |
| Database | MySQL 5.7+ |
| Modules Required | FA_CRM |
| Modules Optional | WarrantyManagement, ProjectManagement |
| Browser | Chrome, Firefox, or Edge (latest versions) |

### 2.2 Test Data

| Data Type | Quantity | Notes |
|-----------|----------|-------|
| Customers (Debtors) | 5+ | Different customer types |
| Support Teams | 3+ | Support, Technical Support, Billing |
| Tickets | 10+ | Various types, statuses, priorities |
| Users | 3+ | Different permission levels |

---

## 3. UAT Test Cases

### 3.1 Role: Support Agent

#### UAT-SA-001: Create Support Ticket

| Field | Details |
|-------|---------|
| Test ID | UAT-SA-001 |
| Scenario | Support agent creates new ticket |
| Actor | Support Agent (ST_MANAGE_TICKET) |
| Preconditions | Logged in as Support Agent, customer exists |
| Steps | 1. Navigate to Support > All Tickets |
| | 2. Fill ticket form: Subject, Description, Type=Issue, Priority=High |
| | 3. Select customer |
| | 4. Click Create Ticket |
| Expected Result | Ticket created with unique ticket number, appears in list |
| Pass Criteria | Ticket visible in list, customer name displayed |
| Business Value | Core functionality for daily operations |

#### UAT-SA-002: View and Respond to Ticket

| Field | Details |
|-------|---------|
| Test ID | UAT-SA-002 |
| Scenario | Agent views ticket and adds activity |
| Actor | Support Agent (ST_VIEW_TICKET) |
| Preconditions | Ticket exists |
| Steps | 1. Open ticket from list |
| | 2. View all details |
| | 3. Add activity: Call to customer |
| | 4. Add note: Follow-up required |
| Expected Result | Activity and note saved, displayed in respective sections |
| Pass Criteria | Activity appears in activities list, note in notes list |
| Business Value | Tracking communication with customers |

#### UAT-SA-003: Update Ticket Status

| Field | Details |
|-------|---------|
| Test ID | UAT-SA-003 |
| Scenario | Agent updates ticket as work progresses |
| Actor | Support Agent (ST_MANAGE_TICKET) |
| Preconditions | Ticket exists with status "New" |
| Steps | 1. Open ticket |
| | 2. Change status to "InProgress" |
| | 3. Add resolution notes |
| | 4. Save |
| Expected Result | Status updated, changes reflected in ticket list |
| Pass Criteria | New status shows in list |
| Business Value | Progress tracking |

---

### 3.2 Role: Support Manager

#### UAT-SM-001: Assign Ticket to Team Member

| Field | Details |
|-------|---------|
| Test ID | UAT-SM-001 |
| Scenario | Manager assigns ticket to team member |
| Actor | Support Manager (ST_ASSIGN_TICKET) |
| Preconditions | Ticket exists, team member exists |
| Steps | 1. Open ticket |
| | 2. Select "Assigned To" |
| | 3. Select team |
| | 4. Save |
| Expected Result | Ticket assigned, assignment reflected |
| Pass Criteria | Assignment visible in ticket detail |
| Business Value | Workload distribution |

#### UAT-SM-002: Manage Support Teams

| Field | Details |
|-------|---------|
| Test ID | UAT-SM-002 |
| Scenario | Create and manage support teams |
| Actor | Support Manager (ST_ADMIN) |
| Preconditions | None |
| Steps | 1. Navigate to Support > Teams |
| | 2. Create new team "VIP Support" |
| | 3. Assign team leader |
| | 4. Save |
| Expected Result | Team created and available for ticket assignment |
| Pass Criteria | Team appears in team list |
| Business Value | Organizational structure |

#### UAT-SM-003: Review Ticket Metrics

| Field | Details |
|-------|---------|
| Test ID | UAT-SM-003 |
| Scenario | Manager reviews ticket status |
| Actor | Support Manager (ST_VIEW_TICKET) |
| Preconditions | Multiple tickets exist with different statuses |
| Steps | 1. Navigate to Support > All Tickets |
| | 2. View ticket list |
| | 3. Count by status |
| Expected Result | Tickets displayed with correct status counts |
| Pass Criteria | Status breakdown visible |
| Business Value | Performance monitoring |

---

### 3.3 Role: Customer Service Representative

#### UAT-CS-001: Create Ticket from Customer Call

| Field | Details |
|-------|---------|
| Test ID | UAT-CS-001 |
| Scenario | CSR logs customer issue as ticket |
| Actor | CSR (ST_MANAGE_TICKET) |
| Preconditions | Customer on phone with issue |
| Steps | 1. Navigate to Support > All Tickets |
| | 2. Create ticket with customer info |
| | 3. Type = Question |
| | 4. Priority = Medium |
| | 5. Add initial note with call details |
| Expected Result | Ticket created, linked to customer |
| Pass Criteria | Customer name appears in ticket |
| Business Value | Customer service logging |

#### UAT-CS-002: Link Ticket to Invoice

| Field | Details |
|-------|---------|
| Test ID | UAT-CS-002 |
| Scenario | Link ticket to customer invoice for billing |
| Actor | CSR (ST_MANAGE_TICKET) |
| Preconditions | Ticket exists, invoice exists |
| Steps | 1. Open ticket |
| | 2. Add service item with cost |
| | 3. Link to customer invoice |
| Expected Result | Item added, can be linked to invoice |
| Pass Criteria | Item visible with correct pricing |
| Business Value | Billing tracking |

---

### 3.4 Integration Scenarios

#### UAT-INT-001: CRM Integration - Customer History

| Field | Details |
|-------|---------|
| Test ID | UAT-INT-001 |
| Scenario | View customer tickets from CRM |
| Actor | Support Agent |
| Preconditions | Customer has existing tickets |
| Steps | 1. Create ticket linked to customer |
| | 2. View ticket list filtered by customer |
| Expected Result | Customer tickets visible |
| Pass Criteria | All customer tickets displayed |
| Business Value | Customer history visibility |

#### UAT-INT-002: Warranty Claim Integration

| Field | Details |
|-------|---------|
| Test ID | UAT-INT-002 |
| Scenario | Create ticket for warranty claim |
| Actor | Support Agent |
| Preconditions | WarrantyManagement module installed, warranty exists |
| Steps | 1. Create ticket with warranty_id |
| | 2. Save ticket |
| | 3. Check warranty status |
| Expected Result | Warranty status updated to "Claimed" |
| Pass Criteria | Warranty shows Claimed status |
| Business Value | Automated warranty processing |

#### UAT-INT-003: RMA Integration

| Field | Details |
|-------|---------|
| Test ID | UAT-INT-003 |
| Scenario | Ticket updated when RMA created |
| Actor | Support Agent |
| Preconditions | RMA module available |
| Steps | 1. Create ticket |
| | 2. Simulate RMA creation event |
| | 3. Check ticket status |
| Expected Result | Ticket status updated to "InProgress" with RMA note |
| Pass Criteria | Status reflects RMA |
| Business Value | Cross-module workflow |

---

### 3.5 Security Scenarios

#### UAT-SEC-001: Unauthorized Access Blocked

| Field | Details |
|-------|---------|
| Test ID | UAT-SEC-001 |
| Scenario | User without permission cannot access |
| Actor | Basic User (no ST permissions) |
| Preconditions | User lacks ST_VIEW_TICKET |
| Steps | 1. Attempt to access Support > All Tickets |
| Expected Result | Access denied, redirected or error shown |
| Pass Criteria | No ticket data visible |
| Business Value | Data security |

#### UAT-SEC-002: Admin Functions Restricted

| Field | Details |
|-------|---------|
| Test ID | UAT-SEC-002 |
| Scenario | Non-admin cannot manage teams |
| Actor | Support Agent (no ST_ADMIN) |
| Preconditions | User lacks ST_ADMIN |
| Steps | 1. Attempt to access Support > Teams |
| Expected Result | Access denied |
| Pass Criteria | Teams page not accessible |
| Business Value | Role-based access |

---

### 3.6 End-to-End Scenarios

#### UAT-E2E-001: Complete Ticket Lifecycle

| Field | Details |
|-------|---------|
| Test ID | UAT-E2E-001 |
| Scenario | Full ticket lifecycle from creation to closure |
| Actor | Support Agent |
| Preconditions | Customer exists |
| Steps | 1. Create ticket (status=New) |
| | 2. Assign to team member |
| | 3. Add call activity logged |
| | 4. Add internal note |
| | 5. Add service item |
| | 6. Update status to InProgress |
| | 7. Update status to Resolved |
| | 8. Update status to Closed |
| Expected Result | All steps complete, full history maintained |
| Pass Criteria | Complete audit trail in activities and notes |
| Business Value | Complete workflow validation |

#### UAT-E2E-002: High Priority Issue Escalation

| Field | Details |
|-------|---------|
| Test ID | UAT-E2E-002 |
| Scenario | Critical issue handled with escalation |
| Actor | Support Agent -> Manager |
| Preconditions | Team structure exists |
| Steps | 1. Create critical priority ticket |
| | 2. Assign to team |
| | 3. Manager reassigns to specialist |
| | 4. Add resolution activity |
| | 5. Resolve ticket |
| Expected Result | Ticket properly escalated and resolved |
| Pass Criteria | All actions tracked with timestamps |
| Business Value |Escalation process validation |

---

## 4. UAT Sign-Off Criteria

### 4.1 Test Case Status

| Category | Required Pass Rate |
|----------|-------------------|
| Critical Test Cases | 100% |
| Major Test Cases | 100% |
| Minor Test Cases | > 90% |

### 4.2 Defect Severity Definitions

| Severity | Definition | Must Fix Before Sign-Off |
|----------|------------|--------------------------|
| Critical | System crash, data loss, security breach | Yes |
| Major | Core feature not working | Yes |
| Minor | UI issue, cosmetic | No |

### 4.3 Sign-Off Requirements

UAT sign-off requires:
- All critical test cases passed
- All major test cases passed
- No critical or major defects open
- Business representative approval

---

## 5. Test Execution Log

### 5.1 Execution Template

| Test ID | Scenario | Tester | Date | Result | Defect ID |
|---------|----------|--------|------|--------|-----------|
| UAT-SA-001 | Create Support Ticket | | | | |
| UAT-SA-002 | View and Respond | | | | |
| UAT-SM-001 | Assign Ticket | | | | |

### 5.2 Defect Log Template

| Field | Description |
|-------|-------------|
| Defect ID | Sequential number (DEF-001, DEF-002...) |
| Test ID | Related UAT test case |
| Severity | Critical/Major/Minor |
| Description | Detailed description |
| Steps to Reproduce | How to reproduce |
| Expected | What should happen |
| Actual | What happened |
| Resolution | Fix applied |
| Retest Date | Date of retest |
| Retest Result | Pass/Fail |

---

## 6. UAT Participants

| Role | Name | Responsibilities |
|------|------|------------------|
| Business Lead | | Requirements sign-off |
| Support Manager | | Team management scenarios |
| Support Agent | | Ticket handling scenarios |
| CSR | | Customer-facing scenarios |
| IT Representative | | Technical validation |

---

## 7. Schedule

| Activity | Duration | Day |
|----------|----------|-----|
| Environment Setup | 1 day | Day 1 |
| UAT Test Execution | 3 days | Day 2-4 |
| Defect Fixing | 2 days | Day 5-6 |
| Retest | 1 day | Day 7 |
| Sign-off | 1 day | Day 8 |

---

## 8. Risk Assessment

| Risk | Impact | Mitigation |
|------|--------|------------|
| Incomplete test data | Medium | Prepare data in advance |
| Environment issues | High | Use stable test environment |
| Unclear requirements | Medium | Refer to Functional Requirements |
| Dependency on other modules | Medium | Test with and without optional modules |

---

## 9. UAT Acceptance

### 9.1 Acceptance Statement

By signing below, the business stakeholders confirm that the Support Tickets module has been tested according to this UAT plan and meets the defined acceptance criteria.

| Role | Name | Signature | Date |
|------|------|-----------|------|
| Business Lead | | | |
| IT Lead | | | |
| Project Manager | | | |

---

## 10. Quick Reference

### 10.1 Menu Paths

| Function | Menu Path |
|----------|-----------|
| All Tickets | Support > All Tickets |
| My Tickets | Support > My Tickets |
| Teams | Support > Teams |

### 10.2 Ticket Statuses

| Status | Description |
|--------|-------------|
| New | Newly created ticket |
| InProgress | Being worked on |
| Waiting | Waiting on customer/third party |
| Resolved | Issue resolved |
| Closed | Ticket closed |

### 10.3 Priority Levels

| Priority | Description |
|----------|-------------|
| Low | Minor issue, can wait |
| Medium | Normal priority |
| High | Important, needs attention |
| Critical | Urgent, system down |

### 10.4 Ticket Types

| Type | Description |
|------|-------------|
| Question | Customer question |
| Issue | Product/service issue |
| Request | Service request |
| Bug | Software bug report |
