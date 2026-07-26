# LeaderHub — Feature Spec: LeadGym (Gym Software)

> **Priority**: A (MVP) + B | **App**: LeadGym (apps/gym) | **Status**: Draft
> **Base**: Fork laravel-gymie (Laravel 12 + Filament 5) dengan kustomisasi UI

## 1. Tujuan
Software gym multi-tenant yang dijual/bundling dengan paket alat gym. Satu
instance melayani banyak gym pelanggan dengan data terisolasi via `tenant_id`.

## 2. Multi-Tenancy

### 2.1 Strategy
- **Single database + `tenant_id`** (bukan DB-per-tenant)
- Alasan: efisiensi maintenance di cPanel (1 DB), update sekali untuk semua tenant
- Pakai `stancl/tenancy` 3.x, mode single-DB

### 2.2 Tenant Identification
- Subdomain: `{slug}.gym.leaderhub.id` (e.g. `demo.gym.leaderhub.id`)
- Custom domain (future, Enterprise tier): `gym.customerdomain.com`
- Wildcard DNS `*.gym.leaderhub.id` → server IP

### 2.3 Isolation
- `BelongsToTenant` trait di semua model LeadGym
- Global scope auto-filter `tenant_id` = current tenant
- Tenant settings per gym (name, logo, color override, capacity, hours)
- Auth terpisah: `gym_users` (per-tenant, bukan shared `users`)

## 3. Modul

### 3.1 Members (Priority A — MVP)
- CRUD member (name, photo, gender, DOB, phone, email, address, emergency contact)
- Member code (per-tenant unique, e.g. `M-0001`)
- QR code/barcode (generated, untuk check-in)
- Membership card (PDF, dengan QR)
- Search & filter (status, plan, join date)
- Import/export CSV
- Member history (subscriptions, check-in, invoices)
- Tenant-scoped

### 3.2 Plans (Priority A — MVP)
- CRUD plan (name, type, duration_days, price, features, is_active)
- Type: membership, pt_sessions, add_on
- Duration: 30/90/365 days, custom
- Features: JSON array (e.g. ["gym_access", "group_class", "sauna"])
- Active/archived status

### 3.3 Subscriptions (Priority A — MVP)
- Create subscription (member + plan, start_date)
- Auto-calculate end_date (start + plan.duration_days)
- Status: active, expired, frozen, transferred, cancelled
- Renewal (extend, new invoice)
- Freeze (pause, extend end_date by frozen_days)
- Transfer (move to another member)
- Expiry notification (7 days before, email/WA)
- History log

### 3.4 Invoices (Priority A — MVP)
- Invoice from subscription (auto on create/renew)
- Manual invoice (services, add-on, penalty)
- Invoice numbering per-tenant: `INV-{tenant-slug}-YYYY-XXXXX`
- Status: unpaid, partial, paid, cancelled
- PDF generation (dompdf, dengan tenant branding)
- Payment record (invoice_transactions): amount, method, date, reference
- Payment method: cash, transfer, card, qris, other

### 3.5 Services (Priority B)
- CRUD service (name, type, price, duration, is_active)
- Type: pt_session, class, locker, towel, other
- Assign to member (member_services): total_sessions, used_sessions
- Track usage (PT sessions remaining)

### 3.6 Enquiries & Follow-ups (Priority B)
- Enquiry CRUD (name, phone, email, source, interest, status)
- Source: walk_in, online, referral, social
- Status: new, contacted, converted, lost
- Follow-up log (date, note, next_action, status)
- Convert to member (jika converted)

### 3.7 Expenses (Priority B)
- Expense CRUD (date, category, amount, description)
- Category management (custom per tenant)
- Monthly summary
- Filter by date range

### 3.8 Check-in (Priority B)
- Scan barcode/QR (camera via browser, atau manual input)
- Validate active subscription
- Log check-in time
- Capacity counter (current vs max from tenant settings)
- Block if subscription expired (show message)
- Check-out (optional, untuk time tracking)

### 3.9 Dashboard (Priority A — MVP)
- Active members count
- Revenue this month
- New members this month
- Expiring soon (7 days)
- Check-in today
- Capacity gauge (current/max)
- Retention rate
- Charts: revenue trend (6 months), member growth, check-in heatmap

### 3.10 Tenant Settings (Priority A — MVP)
- Gym name, logo, address, phone, email
- Color override (within design system range — primary color bisa di-shift)
- Operating hours (per day)
- Capacity (max concurrent)
- License key input & status display
- Staff management (gym_users CRUD, role assign)

## 4. Role (Per-Tenant)

| Role | Akses |
|------|-------|
| `gym_owner` | Semua modul + settings + staff + license |
| `gym_staff` | Members, subscriptions, invoices (view+create), check-in, enquiries |
| `gym_trainer` | Members assigned, services (PT sessions) |

## 5. Customisasi UI dari gymie

### 5.1 Yang Diubah dari laravel-gymie
- Theme: pakai design tokens LeaderHub (accent merah, Inter, modern premium)
- Filament panel config: brandName dari tenant name, logo dari tenant
- Sidebar: grouped (Dashboard, Gym, System)
- Stat card: style LeaderHub (lihat design system §3.7)
- Form: style LeaderHub (lihat design system §3.8)
- Remove fitur yang tidak dipakai (jika ada di gymie yang tidak relevan)
- Tambah: multi-tenant (stancl/tenancy), license validation, capacity tracking

### 5.2 Yang Dipertahankan dari gymie
- Struktur model (Members, Plans, Subscriptions, Services, Invoices, Enquiries, FollowUps, Expenses)
- Filament 5 resource pattern
- Invoice transaction logic
- PDF generation pattern

## 6. License Validation
- Lihat `08-erp-integration.md` §5-6 untuk detail license flow
- Gym owner input license_key di settings → validate via LeaderP API
- Daily sync cron
- Read-only mode if expired/suspended

## 7. Data Source
- Semua tabel LeadGym (lihat `03-database-schema.md` §4)
- Tenant-scoped via global scope

## 8. Acceptance Criteria
1. Multi-tenant: tenant A tidak bisa akses data tenant B
2. Member CRUD berfungsi (dengan QR code)
3. Plan CRUD berfungsi
4. Subscription: create, renewal, freeze, transfer berfungsi
5. Invoice: create, PDF, payment record berfungsi
6. Service & member_service berfungsi (PT sessions)
7. Enquiry & follow-up berfungsi
8. Expense CRUD berfungsi
9. Check-in: scan, validate, capacity berfungsi
10. Dashboard stats & charts berfungsi
11. Tenant settings (branding, capacity, license) berfungsi
12. License validation & read-only mode berfungsi
13. Role per-tenant berfungsi (owner/staff/trainer)
14. UI konsisten dengan design system LeaderHub
15. Responsive (tablet ke atas)