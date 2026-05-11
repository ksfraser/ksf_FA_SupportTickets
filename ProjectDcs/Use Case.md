# Use Cases - ksf_FA_SupportTickets

## Overview
ksf_FA_SupportTickets provides the FrontAccounting UI for ksf_SupportTickets.

## Reference Use Cases
- Core UC: ksf_SupportTickets/ProjectDcs/Use Case.md (UC-ST-001 through UC-ST-013)

---

## UC-FA-ST-001: Ticket Entry via FA Form
**Actor**: Support Agent (FA User)

**FA-Specific Flow**:
1. Navigate: Support > New Ticket
2. FA form with:
   - Customer selection (FA-CRM integration)
   - Subject, description
   - Priority selector
   - FA dimension selection
3. Save creates ticket in `fa_tickets` table
4. Auto-assigns based on queue rules

---

## UC-FA-ST-002: Customer Ticket View
**Actor**: Support Agent

**FA-Specific Flow**:
1. Open customer in FA-CRM
2. View "Support Tickets" tab
3. See all tickets for customer:
   - Open tickets
   - Resolution history
   - SLA status
4. Click ticket → opens FA ticket form

---

## UC-FA-ST-003: Ticket Dimensions
**Actor**: Support Manager, Finance

**FA-Specific Flow**:
1. Assign dimension to ticket (department, project)
2. Generate reports by dimension
3. SLA calculations respect FA dimensions

## Reference Use Cases
- Core UC: ksf_SupportTickets/ProjectDcs/Use Case.md

*Document Version: 1.0.0*
*Last Updated: 2026-05-11*