# LeaderHub — Feature Spec: Admin Panel (LeaderP & LeadGym)

> **Priority**: A (MVP) | **App**: LeaderP (Hub) + LeadGym (Gym) | **Status**: Draft

## 1. Tujuan
Panel admin terpadu untuk ERP distributor (LeaderP) dan software gym
(LeadGym), berbasis Filament 5 dengan UI konsisten (design system sama).

## 2. LeaderP (ERP — apps/hub)

### 2.1 Akses
- URL: `https://erp.leaderhub.id`
- Auth: Filament auth (email + password)
- Roles: Filament Shield (super_admin, content_admin, sales_admin, inventory_admin, accounting_admin, hr_admin, company_admin, license_admin)

### 2.2 Dashboard
```
┌─────────────────────────────────────────────┐
│ TOPBAR: Logo LEADERP | User | Dark Mode | Logout │
├──────────┬──────────────────────────────────┤
│ SIDEBAR  │ DASHBOARD                        │
│          │ ┌────┐ ┌────┐ ┌────┐ ┌────┐     │
│ Dashboard│ │Stat│ │Stat│ │Stat│ │Stat│     │
│ ─── ERP  │ └────┘ └────┘ └────┘ └────┘     │
│ Inventory│                                  │
│ Purchases│ Recent Inquiries │ Low Stock    │
│ Sales    │ ┌──────────────┐ ┌────────────┐ │
│ After    │ │ INQ-2026-001 │ │ Treadmill  │ │
│  Sales   │ │ ...          │ │ 0 unit     │ │
│ Documents│ └──────────────┘ └────────────┘ │
│ Accounts │                                  │
│ HRM      │ Expiring Licenses │ Charts      │
│ Company  │ ┌──────────────┐ ┌────────────┐ │
│ ─── Lead │ │ License X    │ │ Revenue    │ │
│ Licenses │ │ expires 7d   │ │ chart      │ │
│ ─── Sys  │ └──────────────┘ └────────────┘ │
│ DB Mgmt  │                                  │
│ Settings │                                  │
│ Users    │                                  │
└──────────┴──────────────────────────────────┘
```

### 2.3 Sidebar Navigation
| Group | Items |
|-------|-------|
| **Dashboard** | Overview |
| **ERP** | Inventory, Purchases, Sales, After Sales, Documents, Accounting, HRM, Company Admin |
| **LeadGym** | License Customers, License Plans, Licenses, Activations |
| **Website** | Inquiries, Gallery, Banners, Blog, Pages, Settings, Translations |
| **System** | DB Management, Users & Roles, Settings |

### 2.4 Stat Cards (Dashboard)
- Total Revenue (month)
- New Inquiries (week)
- Low Stock Products (count)
- Active Licenses (LeadGym)
- Expiring Licenses (7 days)
- Pending Installations
- Open Support Tickets

### 2.5 Resource Patterns
- List: table dengan search, filter, sort
- Create/Edit: form dengan sections, wizard untuk complex (PO, SO)
- View: detail page dengan relation managers
- Actions: custom action buttons (Convert to SO, Receive Goods, Suspend License)
- Export: CSV/PDF (maatwebsite/excel, dompdf)

## 3. LeadGym (Gym Software — apps/gym)

### 3.1 Akses
- URL: `https://gym.leaderhub.id` (central, untuk login/select tenant)
- Tenant URL: `https://{tenant}.gym.leaderhub.id` (per gym)
- Auth: Filament auth (gym_users, per-tenant)
- Roles: gym_owner, gym_staff, gym_trainer

### 3.2 Multi-Tenant
- stancl/tenancy 3.x, single-DB mode
- Tenant identification: subdomain (`{slug}.gym.leaderhub.id`)
- Global scope: auto-filter `tenant_id` di semua model LeadGym
- Tenant settings: branding (name, logo, color override), capacity, operating hours

### 3.3 Dashboard (per tenant)
```
┌─────────────────────────────────────────────┐
│ TOPBAR: [Gym Name] | User | Logout          │
├──────────┬──────────────────────────────────┤
│ SIDEBAR  │ DASHBOARD                        │
│          │ ┌────┐ ┌────┐ ┌────┐ ┌────┐     │
│ Dashboard│ │Stat│ │Stat│ │Stat│ │Stat│     │
│ ─── Gym  │ └────┘ └────┘ └────┘ └────┘     │
│ Members  │                                  │
│ Plans    │ Active Members │ Revenue Month   │
│ Subs     │ ┌────────────┐ ┌────────────┐   │
│ Services │ │ 847        │ │ Rp 45.2M   │   │
│ Invoices │ └────────────┘ └────────────┘   │
│ Enquiries│                                  │
│ Expenses │ Check-in Today │ Expiring 7d    │
│ Check-in │ ┌────────────┐ ┌────────────┐   │
│ ─── Sys  │ │ 32         │ │ 12 members │   │
│ Settings │ └────────────┘ └────────────┘   │
│ Staff    │                                  │
│ License  │ Capacity Gauge │ Retention Chart│
└──────────┴──────────────────────────────────┘
```

### 3.4 Sidebar Navigation
| Group | Items |
|-------|-------|
| **Dashboard** | Overview |
| **Gym** | Members, Plans, Subscriptions, Services, Invoices, Enquiries, Follow-ups, Expenses, Check-in |
| **System** | Settings, Staff (gym_users), License Status |

### 3.5 Stat Cards (Dashboard)
- Active Members (count)
- Revenue This Month
- New Members This Month
- Expiring Subscriptions (7 days)
- Check-in Today
- Capacity Gauge (current/max)
- Retention Rate
- Open Enquiries

## 4. Konsistensi UI Antar App

### 4.1 Design Tokens
- Sama persis (dari `packages/design-tokens/`)
- Filament theme: primary = #E10600, font Inter, dark default
- Custom CSS: heading rapat (letter-spacing -0.02em), focus ring aksen, radius lembut

### 4.2 Layout
- Sidebar: collapsible, dark bg
- Topbar: brand name (LEADERP / LEADGYM / gym name), user menu, dark mode
- Content: max-width, padding 24px

### 4.3 Komponen
- Stat card: lihat `04-design-system.md` §3.7
- Data table: lihat `04-design-system.md` §3.6
- Form input: lihat `04-design-system.md` §3.8
- Badge: lihat `04-design-system.md` §3.9
- Modal: lihat `04-design-system.md` §3.10

## 5. Role & Permission (Filament Shield)
- Super admin: semua
- Per role: policy per resource (view, create, update, delete, restore)
- Custom permissions: `convert_inquiry`, `receive_po`, `suspend_license`
- LeadGym: per-tenant scoped (gym_owner all, gym_staff limited, gym_trainer PT only)

## 6. Acceptance Criteria
1. LeaderP login berfungsi (Filament auth)
2. LeadGym login berfungsi (per-tenant)
3. Dashboard kedua app menampilkan stats
4. Sidebar navigation lengkap & grouped
5. Role-based access berfungsi (Shield)
6. Multi-tenant isolation LeadGym (no cross-tenant data)
7. UI konsisten (design tokens sama)
8. Dark mode default
9. Responsive (tablet ke atas)
10. Export CSV/PDF berfungsi