# LeaderHub — Feature Spec: ERP Integration & License Manager

> **Priority**: A (MVP) + B | **App**: LeaderP + LeadGym | **Status**: Draft

## 1. Tujuan
Mendokumentasikan integrasi antara website ↔ LeaderP ↔ LeadGym, khususnya:
- Shared database via core package
- Inquiry → Sales Order flow
- License Manager (LeaderP) ↔ LeadGym validation

## 2. Shared Database (Core Package)

### 2.1 Konsep
Ketiga app (website, hub, gym) share satu database MySQL via `leaderhub/core`.
Semua migrations ada di `core/database/migrations/`. Model di `core/src/Models/`.

### 2.2 Migration Order
Saat deploy, tiap app jalankan:
```bash
php artisan migrate --path=../../core/database/migrations --force
php artisan migrate --force  # app-specific migrations (jika ada)
```

### 2.3 Model Access
```php
// Dari apps/website
use Leaderhub\Core\Models\Product;
$products = Product::where('status', 'published')->get();

// Dari apps/hub (LeaderP)
use Leaderhub\Core\Models\SalesOrder;
$so = SalesOrder::find(1);

// Dari apps/gym (LeadGym) — tenant-scoped
use Leaderhub\Core\Models\Member;  // punya BelongsToTenant trait
$members = Member::all();  // auto-filter tenant_id
```

## 3. Inquiry → Sales Order Flow

### 3.1 Flow
```
Website: Visitor submit inquiry
  └→ POST /inquiry/submit
     └→ InquiryService::create($data)
        └→ Insert inquiries + inquiry_items
        └→ Generate INQ-YYYY-XXXXX
        └→ Email admin

LeaderP: Sales admin view inquiry
  └→ InquiryResource → view inquiry detail
     └→ Click "Convert to Sales Order"
        └→ SalesOrderService::createFromInquiry($inquiry)
           └→ Create sales_orders (copy customer info)
           └→ Create sales_order_items (from inquiry_items, set price IDR)
           └→ Update inquiry.status = 'converted'
           └→ Update inquiry.converted_sales_order_id
           └→ Redirect ke SO edit (review price, qty, delivery)
```

### 3.2 SalesOrderService
```php
class SalesOrderService
{
    public function createFromInquiry(Inquiry $inquiry): SalesOrder
    {
        return DB::transaction(function () use ($inquiry) {
            $so = SalesOrder::create([
                'so_number' => $this->generateNumber(),
                'inquiry_id' => $inquiry->id,
                'customer_name' => $inquiry->customer_name,
                'customer_phone' => $inquiry->customer_phone,
                'customer_email' => $inquiry->customer_email,
                'order_date' => now(),
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            foreach ($inquiry->items as $item) {
                $so->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price_idr' => $item->product->sale_price_idr,
                    'total_idr' => $item->product->sale_price_idr * $item->quantity,
                ]);
            }

            $inquiry->update([
                'status' => 'converted',
                'converted_sales_order_id' => $so->id,
            ]);

            return $so;
        });
    }
}
```

## 4. License Manager (LeaderP)

### 4.1 Modul
- LicenseCustomerResource: CRUD customer gym (company, contact)
- LicensePlanResource: CRUD plan (basic, pro, enterprise — features, price)
- LicenseResource: CRUD license (issue key, expiry, status, suspend)
- LicenseActivationResource: log aktivasi per tenant

### 4.2 Issue License Flow
```
Admin (LeaderP) create license customer
  └→ Create license: select customer, plan, billing cycle, start date
     └→ Generate license_key (UUID-like)
     └→ Set expires_at (start + 1 month/year)
     └→ Status = active
     └→ (Opsional) Create tenant di LeadGym, link license_id
     └→ Kirim license_key ke customer (email)
```

### 4.3 Suspend/Expire
```
Admin suspend license
  └→ status = 'suspended', suspended_reason
     └→ LeadGym sync (daily cron) detect status change
     └→ LeadGym set tenant.license_status = 'suspended'
     └→ LeadGym read-only mode + banner

Auto-expire (cron di LeaderP):
  └→ Daily: cek licenses WHERE expires_at < today AND status = active
     └→ Update status = 'expired'
     └→ LeadGym sync detect → tenant read-only
```

## 5. License Validation API (LeaderP)

### 5.1 Endpoint
```
POST https://erp.leaderhub.id/api/license/validate
Authorization: Bearer {api_token}  (Sanctum, per-tenant token)
Content-Type: application/json

Body:
{
  "license_key": "LDR-GYM-XXXXXXXX",
  "tenant_id": 1
}

Response 200:
{
  "valid": true,
  "status": "active",
  "plan": "pro",
  "features": ["members", "subscriptions", "invoices", "check_in", "expenses"],
  "expires_at": "2026-12-31",
  "max_members": 500
}

Response 200 (invalid):
{
  "valid": false,
  "status": "expired",
  "message": "License expired on 2026-06-30"
}
```

### 5.2 Rate Limit
- 60 request per menit per IP
- API token required (Sanctum)

### 5.3 Logging
- Setiap validasi log ke `license_validations` (license_id, tenant_id, valid, status, ip, validated_at)

## 6. LeadGym License Validation

### 6.1 Setup
- Gym owner input license_key di Tenant Settings
- LeadGym call LeaderP API untuk validate
- Store result di `tenants.license_status`, `tenants.license_expires_at`

### 6.2 Validation Flow
```
Gym owner input license_key → save
  └→ LicenseValidatorService::validate($tenant)
     └→ POST erp.leaderhub.id/api/license/validate
        └→ Response: valid? status? features? expires_at?
           └→ Update tenant.license_status, license_expires_at
           └→ If valid → enable features per plan
           └→ If invalid → read-only mode + banner
```

### 6.3 Daily Sync (Cron)
```
LeadGym cron (daily 00:00):
  └→ php artisan gym:sync-licenses
     └→ Untuk setiap tenant:
        └→ LicenseValidatorService::validate($tenant)
        └→ Update status
        └→ Notify gym owner if status change (active→expired/suspended)
```

### 6.4 Read-Only Mode
- Jika license_status = expired/suspended:
  - Disable create/edit/delete di semua resource
  - Show banner: "Lisensi Anda {expired/suspended}. Hubungi LeaderHub untuk perpanjang."
  - Data tetap bisa dilihat (read-only)
  - Check-in tetap berfungsi (critical operation) tapi dengan warning

## 7. Feature Tier (License Plan)

| Feature | Basic | Pro | Enterprise |
|---------|-------|-----|------------|
| Members | 200 | 1000 | Unlimited |
| Plans & Subscriptions | ✅ | ✅ | ✅ |
| Invoices | ✅ | ✅ | ✅ |
| Check-in | ✅ | ✅ | ✅ |
| Services (PT) | ❌ | ✅ | ✅ |
| Enquiries & Follow-ups | ❌ | ✅ | ✅ |
| Expenses | ❌ | ✅ | ✅ |
| Custom Branding | ❌ | ❌ | ✅ |
| Custom Domain | ❌ | ❌ | ✅ |
| API Access | ❌ | ❌ | ✅ |
| Max Staff | 3 | 10 | Unlimited |

Feature flags dari `license_plans.features` (JSON), di-cache di tenant.

## 8. Acceptance Criteria
1. Shared DB: product di LeaderP → tampil di website
2. Inquiry dari website → muncul di LeaderP
3. Convert inquiry → SO berfungsi
4. License issue di LeaderP → LeadGym tenant bisa aktivasi
5. License validate API berfungsi (return valid/status/features)
6. License suspend di LeaderP → LeadGym read-only (after sync)
7. License expire (auto) → LeadGym read-only
8. Daily sync cron berjalan
9. Feature tier enforced (basic tidak bisa akses services)
10. Rate limit API 60/min berfungsi
11. Read-only mode: data viewable, create/edit disabled
12. Banner tampil saat license invalid