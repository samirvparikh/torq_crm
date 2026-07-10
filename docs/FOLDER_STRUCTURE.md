# LeadCRM — Folder Structure

Production-ready directory layout for the IndiaMART Lead Management CRM.

```
indiamart_lead/
├── app/
│   ├── Enums/                    # Lead status, priority, source enums
│   ├── Events/                   # Domain events (LeadCreated, LeadAssigned, etc.)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/            # Web controllers (Dashboard, Leads, Settings)
│   │   │   └── Api/              # REST API controllers
│   │   ├── Middleware/           # Custom middleware (role checks, etc.)
│   │   ├── Requests/
│   │   │   ├── Lead/             # Lead form request validation
│   │   │   ├── Customer/
│   │   │   ├── Quotation/
│   │   │   └── Settings/
│   │   └── Resources/            # API JSON resources
│   ├── Jobs/
│   │   └── IndiaMart/            # Background sync & import jobs
│   ├── Listeners/                # Event listeners
│   ├── Models/                   # Eloquent models
│   ├── Notifications/            # Email, SMS, WhatsApp, in-app notifications
│   ├── Policies/                 # Authorization policies
│   ├── Providers/                # Service providers
│   ├── Repositories/
│   │   └── Contracts/            # Repository interfaces
│   └── Services/
│       ├── IndiaMart/            # IndiaMART API integration
│       ├── LeadService.php
│       ├── CustomerService.php
│       └── ...
├── bootstrap/
├── config/
│   ├── indiamart.php             # IndiaMART API & sync settings
│   └── leadcrm.php               # Application settings
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── docs/
│   ├── FOLDER_STRUCTURE.md
│   ├── INSTALLATION.md
│   └── DEPLOYMENT.md
├── public/                       # Web root (point Apache/Nginx here)
├── resources/
│   ├── css/
│   ├── js/
│   │   └── modules/              # jQuery/Ajax modules per feature
│   └── views/
│       ├── components/           # Reusable Blade components
│       ├── layouts/              # Main app layout, sidebar, header
│       ├── dashboard/
│       ├── leads/
│       ├── customers/
│       ├── companies/
│       ├── products/
│       ├── quotations/
│       ├── tasks/
│       ├── reports/
│       ├── settings/
│       ├── users/
│       └── indiamart/
├── routes/
│   ├── web.php
│   ├── api.php
│   └── console.php
├── storage/
│   └── app/
│       ├── attachments/
│       └── quotations/
└── tests/
    ├── Feature/
    └── Unit/
```

## Architecture Layers

| Layer | Location | Responsibility |
|-------|----------|----------------|
| Controllers | `app/Http/Controllers` | HTTP entry, delegate to services |
| Form Requests | `app/Http/Requests` | Input validation |
| Services | `app/Services` | Business logic |
| Repositories | `app/Repositories` | Data access abstraction |
| Models | `app/Models` | Eloquent ORM & relationships |
| Policies | `app/Policies` | Authorization rules |
| Events/Listeners | `app/Events`, `app/Listeners` | Decoupled side effects |
| Jobs | `app/Jobs` | Queue-based background work |
| Notifications | `app/Notifications` | Multi-channel alerts |

## Module Mapping

| Module | Controllers | Views | Services |
|--------|-------------|-------|----------|
| Dashboard | `Admin/DashboardController` | `dashboard/` | `DashboardService` |
| Leads | `Admin/LeadController` | `leads/` | `LeadService` |
| IndiaMART | `Admin/IndiaMartController` | `indiamart/` | `Services/IndiaMart/` |
| Customers | `Admin/CustomerController` | `customers/` | `CustomerService` |
| Quotations | `Admin/QuotationController` | `quotations/` | `QuotationService` |
| Reports | `Admin/ReportController` | `reports/` | `ReportService` |
| Settings | `Admin/SettingController` | `settings/` | `SettingService` |

## Next Steps (Build Order)

1. ~~Laravel 12 scaffold~~ ✓
2. ~~Auth & RBAC (Breeze + Spatie Permission)~~ ✓
3. ~~Database migrations~~ ✓
4. ~~Models & relationships~~ ✓
5. ~~Core services & controllers~~ ✓
6. IndiaMART integration module
7. UI shell & modules
8. Tests & deployment docs
