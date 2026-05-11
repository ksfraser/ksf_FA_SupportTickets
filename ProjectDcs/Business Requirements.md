# Business Requirements - ksf_FA_SupportTickets

## Overview
ksf_FA_SupportTickets is the FrontAccounting adapter for ksf_SupportTickets (Helpdesk/Support Case Management).

## Relationship to Core Module

### Core Module
- **ksf_SupportTickets**: Business logic (entity classes, services)
- Namespace: `Ksfraser\SupportTickets`

### FA Adapter
- **ksf_FA_SupportTickets**: FA presentation and persistence layer
- Namespace: `Ksfraser\FA\SupportTickets`
- Integrates with FA users, dimensions, bank accounts

## FA-Specific Features

### Database Integration
- FA-compliant table naming: `fa_tickets`, `fa_ticket_comments`, etc.
- Links to FA users for agent assignment
- Dimensions for ticket reporting

### UI Integration
- FA menu entries
- FA theme/styling
- Extension of customer view for tickets

### Integration with FA-CRM
- Tickets linked to CRM customers
- Customer context in ticket view
- Ticket history in customer timeline

## Link to Core BR
This adapter implements requirements defined in `/home/kevin/Documents/ksf_SupportTickets/ProjectDcs/Business Requirements.md`

## Dependencies
- FrontAccounting core
- ksf_SupportTickets (core)
- ksf_FA_CRM (customer integration)

*Document Version: 1.0.0*
*Last Updated: 2026-05-11*