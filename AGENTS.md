# AGENTS.md - ksf_FA_SupportTickets#

## Architecture Overview#

**FA Module** for Support Ticket System - tickets, SLA tracking, and customer communication.

### Core Principles#
- **SOLID**, **DRY**, **TDD**, **DI**, **SRP**#

## Repository Structure#

```
ksf_FA_SupportTickets/
├── sql/#
│   ├── fa_support_tickets.sql#
│   ├── fa_ticket_replies.sql#
│   ├── fa_ticket_categories.sql#
│   └── fa_ticket_sla.sql#
├── includes/#
│   ├── tickets_db.inc#
│   ├── replies_db.inc#
│   ├── categories_db.inc#
│   └── sla_db.inc#
├── pages/#
├── hooks.php#
├── composer.json#
└── ProjectDocs/#
```

## Dependencies#

- **ksf_FA_SupportTickets_Core** (business logic)#
- **ksf_FA_CRM** (link tickets to contacts)#
- **FrontAccounting 2.4+**#
