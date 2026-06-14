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

## Development Workflow

All development is done in the **devel tree** (`~/Documents/ksf_FA_SupportTickets`). Do **not** edit files in the UAT bind point directly.

### Workflow Steps
1. **Develop** in this repo (feature branches preferred)
2. **Test**: run repo-appropriate tests
3. **Lint**: `php -l` on modified PHP files (no syntax errors)
4. **Commit** and **Push** branch to GitHub
5. **Merge** to `master` when ready
6. **Push** `master` to GitHub
7. **Deploy** to UAT by pulling in the Infrastructure bind point:

   ```
   cd ~/ksf_Infrastructure/fa_modules/ksf_FA_SupportTickets
   git stash -u
   git pull origin master
   git stash pop
   ```

### UAT Bind Point
| Path | Purpose |
|------|---------|
| `~/Documents/ksf_FA_SupportTickets` | Devel tree — all development, testing, commits |
| `~/ksf_Infrastructure/fa_modules/ksf_FA_SupportTickets` | UAT bind point — deployment target, integration testing (if mirrored) |

