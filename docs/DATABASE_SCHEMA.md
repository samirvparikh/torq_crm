# LeadCRM — Database Schema

Complete normalized schema for the IndiaMART Lead Management CRM.

## Entity Relationship Overview

```
users ──┬── leads (assigned_to, created_by)
        ├── lead_assignments
        ├── lead_followups
        ├── lead_notes
        ├── lead_activities
        ├── lead_status_logs
        ├── tasks
        ├── quotations
        └── attachments

lead_sources ── leads
categories ──┬── leads
             └── products

companies ──┬── customers
            ├── leads
            └── quotations

customers ──┬── customer_contact_persons
            ├── customer_addresses
            ├── leads
            └── quotations

leads ──┬── lead_products
        ├── lead_assignments
        ├── lead_followups
        ├── lead_notes
        ├── lead_activities
        ├── lead_status_logs
        ├── quotations
        ├── tasks
        ├── api_logs
        ├── webhook_logs
        └── indiamart_raw_logs

quotations ── quotation_items ── products
```

## Tables (28 total)

| Table | Purpose |
|-------|---------|
| `users` | System users (Breeze auth + CRM fields) |
| `roles` / `permissions` | Spatie RBAC |
| `lead_sources` | IndiaMART, Website, Facebook, etc. |
| `categories` | Product/lead categories (hierarchical) |
| `companies` | Company master data |
| `customers` | Customer profiles |
| `customer_contact_persons` | Multiple contacts per customer |
| `customer_addresses` | Billing/shipping addresses |
| `products` | Product catalog |
| `leads` | Core lead records (100k+ optimized indexes) |
| `lead_products` | Products interested per lead |
| `lead_assignments` | Assignment history |
| `lead_followups` | Calls, meetings, reminders |
| `lead_notes` | Internal notes |
| `lead_activities` | Activity timeline |
| `lead_status_logs` | Status change audit trail |
| `quotations` | Quotations with tax/discount |
| `quotation_items` | Line items per quotation |
| `tasks` | Task management |
| `attachments` | Polymorphic file attachments |
| `settings` | Key-value app configuration |
| `holidays` | Holiday calendar |
| `notifications` | In-app notifications |
| `api_logs` | API request/response logs |
| `webhook_logs` | Webhook event logs |
| `indiamart_raw_logs` | Raw IndiaMART JSON payloads |

## Lead Status Values

`New`, `Assigned`, `Contacted`, `Interested`, `Follow Up`, `Quotation Sent`, `Negotiation`, `Won`, `Lost`, `Junk`, `Duplicate`

## Priority Values

`Low`, `Medium`, `High`, `Urgent`

## Key Indexes (Performance)

The `leads` table includes indexes on:
- `indiamart_lead_id` (unique — duplicate prevention)
- `mobile`, `email`, `company_name` (duplicate detection)
- `status`, `priority`, `assigned_to`, `lead_source_id`
- `next_followup_at`, `expected_closing_date`, `created_at`
- Composite: `(status, assigned_to)`, `(lead_source_id, status)`

## Seeders

| Seeder | Data |
|--------|------|
| `RolePermissionSeeder` | 7 roles, 58 permissions |
| `LeadSourceSeeder` | 11 lead sources |
| `CategorySeeder` | 7 default categories |
| `SettingSeeder` | Company, SMTP, SMS, WhatsApp, IndiaMART settings |
| `AdminUserSeeder` | 7 default users (one per role) |

## Migration Commands

```bash
php artisan migrate:fresh --seed
php artisan migrate:status
```
