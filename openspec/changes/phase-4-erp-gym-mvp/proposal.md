## Why

Website sudah punya homepage, katalog, blog, dan inquiry system (Phase 1-3). Tapi inquiry yang masuk belum bisa diproses menjadi sales order, belum ada sistem inventory untuk kelola stok, belum ada purchase order untuk beli barang dari supplier, dan belum ada software gym (LeadGym) yang adalah produk komersial utama untuk dijual/bundling. Phase 4 adalah inti bisnis: membangun LeaderP (ERP distributor) dan LeadGym (software gym multi-tenant) plus License Manager yang menghubungkan keduanya. Tanpa Phase 4, LeaderHub hanya website biasa — bukan platform bisnis terintegrasi.

## What Changes

### LeaderP (ERP — apps/hub)

#### Panel Setup
- Configure HubPanelProvider: theme Modern Premium (accent #DC2626, Inter, radius 10px), navigation groups (Dashboard, ERP, LeadGym, Website, System), dark mode
- Install & configure Filament Shield dengan 8 roles: super_admin, content_admin, sales_admin, inventory_admin, accounting_admin, hr_admin, company_admin, license_admin

#### Migrations (di core — shared DB)
- `suppliers` — name, contact_person, email, phone, address, country, currency (USD default)
- `purchase_orders` — po_number unique, supplier_id FK, warehouse_id FK, order_date, expected_date, received_date, currency USD, subtotal_usd, shipping_usd, duty_usd, total_usd, exchange_rate, total_idr (landed cost), status (draft/sent/partial/received/cancelled), created_by FK users
- `purchase_order_items` — po_id FK cascade, product_id FK restrict, quantity, unit_price_usd, total_usd, received_quantity
- `sales_orders` — so_number unique, inquiry_id FK nullable (nullOnDelete), customer_name/phone/email/address, order_date, delivery_date, subtotal_idr, discount_idr, tax_idr, total_idr, status (draft/confirmed/delivered/invoiced/closed/cancelled), created_by FK users
- `sales_order_items` — so_id FK cascade, product_id FK restrict, quantity, unit_price_idr, total_idr, serial_number nullable
- `sales_activities` — so_id FK cascade, type (lead/contact/demo/quote/order/follow_up), description, scheduled_at, completed_at, created_by FK users
- `employees` — name, department_id FK nullable (nullOnDelete), position, email, phone, hire_date, salary, status
- `departments` — name, head_id FK employees nullable (nullOnDelete)
- `installations` — so_id FK cascade, scheduled_date, technician_id FK employees nullable (nullOnDelete), location, checklist JSON, status (scheduled/in_progress/completed/cancelled), completed_at, sign_off_by, notes
- `warranties` — warranty_number unique, so_id FK cascade, product_id FK cascade, serial_number, customer_name, start_date, end_date, status (active/expired/claimed)
- `warranty_claims` — warranty_id FK cascade, claim_number unique, issue, type (repair/replacement/refund), status (open/in_progress/resolved/rejected), resolution
- `support_tickets` — ticket_number unique, customer_name, customer_phone, subject, description, priority (low/medium/high/urgent), status (open/in_progress/resolved/closed), related_so_id FK nullable (nullOnDelete), assigned_to FK users nullable
- `company_contacts` — type (supplier/vendor/service/government), name, contact_person, email, phone, address, notes
- `agreements` — company_contact_id FK cascade, title, type (contract/mou/partnership), start_date, end_date, file_path, notes
- `license_customers` — company_name, contact_person, email, phone, address, tenant_id nullable (no FK — tenants table dibuat di batch LeadGym, add FK setelah itu)
- `license_plans` — name, tier (basic/pro/enterprise), price_monthly, price_yearly, features JSON, max_members nullable, max_staff nullable, is_active
- `licenses` — license_key unique, customer_id FK license_customers cascade, plan_id FK license_plans restrict, tenant_id nullable (no FK dulu), billing_cycle (monthly/yearly), started_at, expires_at, status (active/suspended/expired/cancelled), suspended_reason, created_by FK users
- `license_activations` — license_id FK cascade, tenant_id, activated_at, deactivated_at, ip_address
- `license_validations` — license_id FK cascade, tenant_id, valid boolean, status, validated_at, ip_address

**PENTING untuk AI agent**: 
- `license_customers.tenant_id` dan `licenses.tenant_id` belum punya FK karena `tenants` table dibuat di migration LeadGym (batch berikutnya di phase yang sama). Jalankan migration LeadGym DULU, lalu add FK via migration terpisah. Atau urutkan migration: LeadGym tenants DULU, lalu license tables.
- `inquiries.converted_sales_order_id` (dari Phase 3) sekarang bisa di-add FK-nya: `ALTER TABLE inquiries ADD CONSTRAINT FK ke sales_orders.id NULL ON DELETE`.

#### Filament Resources (LeaderP)
- **Inventory**: ProductResource (dengan StockLevels relation manager), WarehouseResource, StockLevelResource, SupplierResource, StockMovementResource
- **Purchases**: PurchaseOrderResource (form dengan items repeater USD, action "Receive Goods" → update stock + stock movement + landed cost calc)
- **Sales**: SalesOrderResource (form dengan items repeater IDR, action "Convert from Inquiry" → create SO dari inquiry items, action "Deliver" → stock out + stock movement)
- **After-Sales**: InstallationResource (action "Complete" → auto-create warranty), WarrantyResource, WarrantyClaimResource, SupportTicketResource
- **HRM**: EmployeeResource, DepartmentResource
- **Company Admin**: CompanyContactResource, AgreementResource
- **License Manager**: LicenseCustomerResource, LicensePlanResource, LicenseResource (auto-generate key, action Suspend/Renew), LicenseActivationResource
- **Dashboard widgets**: StatsOverview (new inquiries, low stock, active licenses, expiring licenses)

#### License API
- `POST /api/license/validate` — Sanctum auth, rate limit 60/min/IP
- Input: `license_key`, `tenant_id`
- Output: `{valid, status, plan, features, expires_at, max_members}`
- Log setiap validation ke `license_validations` table

### LeadGym (apps/gym)

#### Multi-Tenant Setup
- Configure stancl/tenancy 3.x: single-DB mode (tenant_id column, bukan DB-per-tenant)
- Tenant identification: subdomain (`{slug}.gym.leaderhub.test` dev / `{slug}.gym.leaderhub.id` prod)
- BelongsToTenant trait di core: global scope auto-filter `tenant_id` = current tenant
- GymPanelProvider: brandName dari tenant name, theme sama dengan LeaderP

#### Migrations (di core)
- `tenants` — uuid unique, name, slug unique, domain nullable, logo, address, phone, email, primary_color nullable, capacity default 50, operating_hours JSON, license_id nullable, license_key nullable, license_status default 'inactive', license_expires_at nullable, status default 'active'
- `gym_users` — tenant_id FK cascade, name, email, password, role (gym_owner/gym_staff/gym_trainer), is_active. Unique `(tenant_id, email)`
- `members` — tenant_id FK cascade, member_code, name, photo, gender, dob, phone, email, address, emergency_contact, emergency_phone, qr_code, status (active/inactive/blacklisted), joined_at. Unique `(tenant_id, member_code)`. Index `(tenant_id, status)`
- `plans` — tenant_id FK cascade, name, type (membership/pt_sessions/add_on), duration_days, price, features JSON, is_active
- `subscriptions` — tenant_id FK cascade, member_id FK cascade, plan_id FK cascade, start_date, end_date, status (active/expired/frozen/transferred/cancelled), frozen_at, frozen_days, transferred_to nullable, amount. Index `(tenant_id, status)`, `(tenant_id, end_date)`
- `services` — tenant_id FK cascade, name, type (pt_session/class/locker/towel/other), price, duration_minutes, is_active
- `member_services` — tenant_id FK cascade, member_id FK cascade, service_id FK cascade, total_sessions, used_sessions, status (active/completed/expired)
- `invoices` — tenant_id FK cascade, invoice_number, member_id FK nullable (nullOnDelete), subscription_id FK nullable (nullOnDelete), issue_date, due_date, subtotal, discount, tax, total, paid_amount, status (unpaid/partial/paid/cancelled), notes, created_by FK gym_users. Unique `(tenant_id, invoice_number)`
- `invoice_items` — invoice_id FK cascade, description, type (subscription/service/add_on/penalty), reference_id nullable, quantity, unit_price, total
- `invoice_transactions` — invoice_id FK cascade, amount, payment_method (cash/transfer/card/qris/other), payment_date, reference, created_by FK gym_users
- `enquiries` — tenant_id FK cascade, name, phone, email nullable, source (walk_in/online/referral/social), interest nullable, status (new/contacted/converted/lost), converted_member_id nullable, notes
- `follow_ups` — tenant_id FK cascade, enquiry_id FK cascade, follow_up_date, note, next_action, status (pending/done), created_by FK gym_users
- `expenses` — tenant_id FK cascade, expense_date, category, amount, description, created_by FK gym_users. Index `(tenant_id, expense_date)`
- `check_ins` — tenant_id FK cascade, member_id FK cascade, subscription_id FK nullable (nullOnDelete), check_in_at, check_out_at, created_by FK gym_users. Index `(tenant_id, check_in_at)`

#### Filament Resources (LeadGym — per-tenant)
- **Member**: MemberResource (CRUD, photo, QR code, member_code auto, import/export CSV)
- **Plan**: PlanResource (CRUD, type, duration, price, features)
- **Subscription**: SubscriptionResource (create with auto end_date, renewal, freeze, transfer, expiry notification)
- **Invoice**: InvoiceResource (numbering per-tenant, items repeater, payment record, PDF generation)
- **Service**: ServiceResource, MemberServiceResource (PT sessions tracking)
- **Enquiry**: EnquiryResource, FollowUpResource
- **Expense**: ExpenseResource
- **CheckIn**: CheckInResource (scan/validate active subscription, capacity counter)
- **Dashboard**: GymStats widget (active members, revenue, check-in today, expiring 7 days)
- **Tenant Settings**: TenantSettingResource (branding, capacity, operating hours, license key input)
- **License Validator**: LicenseValidatorService (call LeaderP API), SyncLicenses cron command
- **Role & Permission**: Shield per-tenant (gym_owner all, gym_staff limited, gym_trainer PT only)

## Capabilities

### New Capabilities
- `erp-inventory`: Inventory management (products, warehouses, stock levels, suppliers, stock movements) via Filament
- `erp-purchases`: Purchase order management (USD, receive goods, landed cost calculation) via Filament
- `erp-sales`: Sales order management (IDR, convert from inquiry, delivery, invoice) via Filament
- `erp-after-sales`: Installation scheduling, warranty auto-create, warranty claims, support tickets
- `erp-license-manager`: License CRUD (issue, suspend, renew, expire), license plans, customers, activations
- `license-api`: REST API endpoint for LeadGym to validate license key
- `gym-multi-tenant`: Multi-tenant setup (stancl/tenancy single-DB, subdomain identification, BelongsToTenant trait)
- `gym-members`: Member management (CRUD, QR code, import/export) per-tenant
- `gym-plans-subscriptions`: Plan & subscription management (create, renewal, freeze, transfer) per-tenant
- `gym-invoices`: Invoice management (numbering per-tenant, items, payment, PDF) per-tenant
- `gym-operations`: Service, enquiry, expense, check-in modules per-tenant
- `gym-license-validation`: License validation via LeaderP API, daily sync, read-only mode if expired

### Modified Capabilities
<!-- None — semua baru -->

## Impact

### Code Affected
- **core/database/migrations/**: ~20 migration files baru
- **core/src/Models/**: ~20 model classes baru
- **core/src/Traits/**: BelongsToTenant trait
- **apps/hub/app/Filament/Resources/**: ~15 Filament resources
- **apps/hub/app/Http/Controllers/Api/**: LicenseController
- **apps/hub/app/Filament/Widgets/**: StatsOverview
- **apps/gym/app/Providers/Filament/**: GymPanelProvider
- **apps/gym/app/Filament/Resources/**: ~10 Filament resources
- **apps/gym/app/Services/**: LicenseValidatorService
- **apps/gym/app/Console/Commands/**: SyncLicenses
- **apps/gym/config/tenancy.php**: tenancy config

### Database
- ~20 tabel baru (ERP + Gym + License)
- FK: banyak, semua dengan cascade/restrict/nullOnDelete sesuai semantics
- Add FK `inquiries.converted_sales_order_id` → `sales_orders.id` (nullOnDelete) — dari Phase 3 yang belum ada FK

### Dependencies
- stancl/tenancy 3.x (sudah install di Phase 0)
- laravel/sanctum (untuk license API)
- bezhansalleh/filament-shield (sudah install di Phase 0)

### Breaking Changes
- Migration `inquiries` dari Phase 3: add FK constraint `converted_sales_order_id` → `sales_orders.id` (migration terpisah setelah sales_orders dibuat)

## Non-goals

- **Accounting module** (Priority D — future, Phase 7+)
- **Documents module** (Priority C — future, Phase 7+)
- **DB Management** (Priority C — future, Phase 7+)
- **Customer Portal B2B** (Tier 2 expansion — future)
- **CRM Lite** (Tier 2 expansion — future)
- **Quotation Builder** (Tier 2 expansion — future)
- **Barcode scanning** (Tier 2 expansion — future)
- **AI Stock Forecasting** (Tier 3 expansion — future)
- **Class & Booking** (Tier 2 expansion — future)
- **Member PWA** (Tier 2 expansion — future)
- **Loyalty/Reward** (Tier 2 expansion — future)
- **White-Label** (Tier 3 expansion — future)

## Reference

- **Step-by-step detail**: `docs/tasks/phase-4-erp-gym-mvp.md`
- **Architecture**: `docs/02-architecture.md` §4 (ERP modules), §5 (LeadGym)
- **Database schema**: `docs/03-database-schema.md` §3.12-3.35 (ERP), §4 (Gym), §5 (License)
- **Feature specs**: `docs/07-features/07-admin-panel.md`, `docs/07-features/08-erp-integration.md`, `docs/07-features/09-leadgym.md`
- **Design system**: `docs/04-design-system.md` §3.6-3.10 (admin components)