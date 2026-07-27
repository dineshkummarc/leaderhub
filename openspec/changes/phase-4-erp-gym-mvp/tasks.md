# Tasks — Phase 4: LeaderP ERP + LeadGym MVP

> **Detail step-by-step**: `docs/tasks/phase-4-erp-gym-mvp.md`
> **Estimasi**: 3 minggu | **Prasyarat**: Phase 3 selesai (inquiry table exists, products/warehouses/stock_levels from Phase 2)

## Prerequisites Verification

Sebelum mulai Phase 4, pastikan ini sudah ada:
- [ ] `products`, `warehouses`, `stock_levels`, `stock_movements` tables exist (Phase 2)
- [ ] `inquiries`, `inquiry_items` tables exist (Phase 3)
- [ ] `users` table exists with roles (Phase 1)
- [ ] Filament admin panel accessible di `/admin` (website) — untuk reference
- [ ] stancl/tenancy installed di apps/gym (Phase 0)
- [ ] Filament Shield installed di apps/hub (Phase 0)

## Migration Order (CRITICAL)

**PENTING untuk AI agent**: Jalankan migration dalam urutan ini. Jangan acak urutan karena ada FK dependency.

1. `create_tenants_table` + `create_gym_users_table` (LeadGym — butuh untuk license FK)
2. ERP tables (suppliers, purchase_orders, po_items, sales_orders, so_items, sales_activities, employees, departments, installations, warranties, warranty_claims, support_tickets, company_contacts, agreements)
3. License tables (license_customers, license_plans, licenses, license_activations, license_validations) — add FK tenant_id → tenants.id
4. `add_inquiry_sales_order_fk` (add FK dari Phase 3 ke sales_orders)
5. LeadGym core tables (members, plans, subscriptions, services, member_services, invoices, invoice_items, invoice_transactions, enquiries, follow_ups, expenses, check_ins)

## Tasks

### Batch 4A: LeaderP (apps/hub) — bisa paralel dengan 4B

#### T01: Configure Hub Filament panel
- [ ] Edit `HubPanelProvider`: theme, colors, font, navigation groups, dark mode, renderHook theme-styles
- [ ] Buat `resources/views/filament/theme-styles.blade.php` (Inter, radius, focus ring)
- **Validation**: `/hub/login` accessible dengan theme Modern Premium
- **Effort**: 2h

#### T02: Migrations — ERP tables (suppliers, PO, SO, stock, warranty, dll)
- [ ] Buat ~15 migration files di core dengan urutan yang benar (suppliers → PO → PO items → SO → SO items → activities → employees → departments → installations → warranties → warranty_claims → support_tickets → company_contacts → agreements)
- [ ] Buat ~15 models di core (Supplier, PurchaseOrder, PurchaseOrderItem, SalesOrder, SalesOrderItem, SalesActivity, Employee, Department, Installation, Warranty, WarrantyClaim, SupportTicket, CompanyContact, Agreement)
- [ ] Migration `add_inquiry_sales_order_fk`: `ALTER TABLE inquiries ADD FK converted_sales_order_id → sales_orders.id NULL ON DELETE`
- **Validation**: migrate berhasil, semua FK ada, models accessible
- **Effort**: 6h

#### T03: Inventory module (Product, Warehouse, StockLevel, Supplier, StockMovement)
- [ ] 5 Filament resources + StockLevelsRelationManager
- **Validation**: CRUD berfungsi, stock level per warehouse bisa di-set, stock movement ter-log
- **Effort**: 4h

#### T04: Purchase module (PurchaseOrderResource + Receive Goods action)
- [ ] PurchaseOrderResource dengan items repeater USD + Receive action (DB transaction: update stock, stock movement, landed cost)
- **Validation**: Create PO, receive → stock naik, landed cost terhitung
- **Effort**: 4h

#### T05: Sales module (SalesOrderResource + Convert Inquiry + Deliver)
- [ ] SalesOrderResource dengan items repeater IDR + Convert Inquiry action + Deliver action (stock out)
- **Validation**: Convert dari inquiry → SO, deliver → stock turun
- **Effort**: 4h

#### T06: Installation, Warranty, After-sales
- [ ] InstallationResource + Complete action (auto-create warranty), WarrantyResource, WarrantyClaimResource, SupportTicketResource
- **Validation**: Schedule install, complete → warranty auto-create, claim bisa dibuat
- **Effort**: 4h

#### T07: License Manager module
- [ ] LicenseCustomerResource, LicensePlanResource, LicenseResource (auto-generate key, Suspend/Renew actions), LicenseActivationResource
- [ ] Migration: license tables dengan FK tenant_id → tenants.id
- **Validation**: Issue license, suspend, renew berfungsi
- **Effort**: 4h

#### T08: License API
- [ ] Install Sanctum, LicenseController (validate endpoint), rate limit 60/min, log ke license_validations
- **Validation**: POST /api/license/validate dengan key valid → return valid:true, dengan key expired → valid:false
- **Effort**: 3h

#### T09: Dashboard widgets (LeaderP)
- [ ] StatsOverview widget: new inquiries, low stock, active licenses, expiring licenses
- **Validation**: Dashboard menampilkan 4 stat cards
- **Effort**: 2h

#### T10: Role & permission (Shield)
- [ ] `php artisan shield:install`, generate roles & policies, seeder untuk 8 roles
- **Validation**: Role-based access berfungsi (inventory_admin hanya bisa akses inventory)
- **Effort**: 2h

### Batch 4B: LeadGym (apps/gym) — bisa paralel dengan 4A

#### T11: Configure stancl/tenancy + Gym Filament panel
- [ ] Configure tenancy config (single-DB, subdomain mode), GymPanelProvider (per-tenant, theme), ResolveTenant middleware
- [ ] Buat Tenant model di core, BelongsToTenant trait di core
- **Validation**: Tenant subdomain resolve, panel load per-tenant
- **Effort**: 4h

#### T12: Migrations — LeadGym tables (tenants, gym_users, members, plans, subs, invoices, dll)
- [ ] Buat ~14 migration files di core (tenants + gym_users DULU, lalu members, plans, subscriptions, services, member_services, invoices, invoice_items, invoice_transactions, enquiries, follow_ups, expenses, check_ins)
- [ ] Buat ~14 models di core dengan BelongsToTenant trait
- **Validation**: migrate berhasil, semua FK ada, models accessible dengan tenant scope
- **Effort**: 6h

#### T13: Member module
- [ ] MemberResource (CRUD, photo, QR code, member_code auto per-tenant, import/export optional)
- **Validation**: CRUD member, QR generate, tenant-scoped (no cross-tenant)
- **Effort**: 4h

#### T14: Plan & Subscription module
- [ ] PlanResource, SubscriptionResource (auto end_date, renewal, freeze, transfer actions)
- **Validation**: CRUD plan, subscription lifecycle (create, renew, freeze, transfer)
- **Effort**: 4h

#### T15: Invoice module
- [ ] InvoiceResource (numbering per-tenant, items repeater, payment record, PDF generation)
- **Validation**: Create invoice, PDF generate, payment record, status auto-update
- **Effort**: 4h

#### T16: Service, Enquiry, Expense, Check-in modules
- [ ] ServiceResource, MemberServiceResource, EnquiryResource, FollowUpResource, ExpenseResource, CheckInResource
- [ ] Check-in validation: active subscription required
- **Validation**: CRUD semua, check-in validate subscription
- **Effort**: 4h

#### T17: Dashboard (LeadGym)
- [ ] GymStats widget: active members, revenue, check-in today, expiring 7 days, capacity gauge
- **Validation**: Dashboard menampilkan stats per-tenant
- **Effort**: 2h

#### T18: Tenant Settings & License validation
- [ ] TenantSettingResource (branding, capacity, hours, license key input), LicenseValidatorService (call LeaderP API), SyncLicenses cron command, read-only mode if expired
- **Validation**: Input license → validate via API, sync cron, read-only if expired
- **Effort**: 4h

#### T19: Role & permission per-tenant (Shield)
- [ ] Shield generate roles (gym_owner, gym_staff, gym_trainer), scoped per-tenant
- **Validation**: Role-based access per-tenant berfungsi
- **Effort**: 2h

#### T20: Customize UI sesuai design system
- [ ] Theme CSS (Inter, accent, radius, focus ring) — sama dengan LeaderP
- **Validation**: UI konsisten dengan LeaderP & website (Modern Premium)
- **Effort**: 1h

## Final Validation

```bash
# LeaderP
cd /run/media/salmon/data/Projects/leaderhub/apps/hub
php artisan migrate:status | grep Pending && echo "✗ Pending" || echo "✓ Migrations OK"
php artisan serve --port=8001 &
sleep 2
curl -s http://localhost:8001/hub/login | grep -q "Filament" && echo "✓ Hub OK" || echo "✗ Hub FAIL"

# License API
curl -s -X POST http://localhost:8001/api/license/validate \
  -H "Content-Type: application/json" \
  -d '{"license_key":"test","tenant_id":1}' \
  | grep -q "valid" && echo "✓ API OK" || echo "✗ API FAIL"
kill %1

# LeadGym
cd ../gym
php artisan migrate:status | grep Pending && echo "✗ Pending" || echo "✓ Migrations OK"
php artisan serve --port=8002 &
sleep 2
curl -s http://localhost:8002/gym/login | grep -q "Filament" && echo "✓ Gym OK" || echo "✗ Gym FAIL"
kill %1

# FK check
php artisan tinker --execute="
use Leaderhub\Core\Models\Tenant;
use Leaderhub\Core\Models\License;
use Leaderhub\Core\Models\SalesOrder;
echo 'Tenants: ' . Tenant::count() . PHP_EOL;
echo 'Licenses: ' . License::count() . PHP_EOL;
echo 'SalesOrders: ' . SalesOrder::count() . PHP_EOL;
"

# Integration: inquiry → SO
php artisan tinker --execute="
use Leaderhub\Core\Models\Inquiry;
\$i = Inquiry::first();
echo 'Inquiry: ' . \$i->inquiry_number . ' | Status: ' . \$i->status . PHP_EOL;
"

cd /run/media/salmon/data/Projects/leaderhub
git add -A && git commit -m "Phase 4: LeaderP ERP + LeadGym MVP + License Manager"
```

**Jika semua ✓ → lanjut Phase 5. Jika ada ✗ → fix dulu, jangan lanjut.**