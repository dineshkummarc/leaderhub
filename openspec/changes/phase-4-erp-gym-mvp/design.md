# Design — Phase 4: LeaderP ERP + LeadGym MVP

## Context
- **Architecture**: `docs/02-architecture.md` §4 (ERP modules), §5 (LeadGym), §6 (data flow)
- **Database schema**: `docs/03-database-schema.md` §3.12-3.35 (ERP tables), §4 (Gym tables), §5 (License tables)
- **Feature specs**: `docs/07-features/07-admin-panel.md`, `docs/07-features/08-erp-integration.md`, `docs/07-features/09-leadgym.md`
- **Step-by-step**: `docs/tasks/phase-4-erp-gym-mvp.md`

## Approach

### Migration Ordering Strategy

**PENTING untuk AI agent**: Urutan migration SANGAT PENTING karena ada dependency antar tabel. Jalankan dalam urutan ini:

1. **Batch 1 — ERP Foundation** (tenants DULU karena license tables reference tenant_id):
   - `create_tenants_table` (LeadGym — butuh untuk license tables)
   - `create_gym_users_table`

2. **Batch 2 — ERP Core**:
   - `create_suppliers_table`
   - `create_warehouses_table` (sudah ada dari Phase 2, skip jika sudah)
   - `create_purchase_orders_table` + `create_purchase_order_items_table`
   - `create_sales_orders_table` + `create_sales_order_items_table` + `create_sales_activities_table`
   - `create_employees_table` + `create_departments_table`
   - `create_installations_table`
   - `create_warranties_table` + `create_warranty_claims_table`
   - `create_support_tickets_table`
   - `create_company_contacts_table` + `create_agreements_table`

3. **Batch 3 — License** (butuh tenants & users):
   - `create_license_customers_table` (add FK tenant_id → tenants.id)
   - `create_license_plans_table`
   - `create_licenses_table` (add FK tenant_id → tenants.id)
   - `create_license_activations_table`
   - `create_license_validations_table`

4. **Batch 4 — Add FK dari Phase 3**:
   - `add_inquiry_sales_order_fk` — `ALTER TABLE inquiries ADD CONSTRAINT FK converted_sales_order_id → sales_orders.id NULL ON DELETE`

5. **Batch 5 — LeadGym Core**:
   - `create_members_table`
   - `create_plans_table`
   - `create_subscriptions_table`
   - `create_services_table` + `create_member_services_table`
   - `create_invoices_table` + `create_invoice_items_table` + `create_invoice_transactions_table`
   - `create_enquiries_table` + `create_follow_ups_table`
   - `create_expenses_table`
   - `create_check_ins_table`

### BelongsToTenant Trait

**PENTING**: Trait ini auto-filter semua query berdasarkan `tenant_id` dari current tenant. Current tenant di-resolve dari subdomain request.

```php
// core/src/Traits/BelongsToTenant.php
namespace Leaderhub\Core\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        // Global scope: auto-filter by tenant_id
        static::addGlobalScope('tenant', function (Builder $builder) {
            $tenantId = app('currentTenant')?->id;
            if ($tenantId) {
                $builder->where('tenant_id', $tenantId);
            }
        });

        // Auto-set tenant_id on create
        static::creating(function (Model $model) {
            $tenantId = app('currentTenant')?->id;
            if ($tenantId && !$model->tenant_id) {
                $model->tenant_id = $tenantId;
            }
        });
    }
}
```

**PENTING untuk AI agent**: 
- Semua model LeadGym (Member, Plan, Subscription, Invoice, dll) HARUS `use BelongsToTenant`
- Model ERP (Product, SalesOrder, dll) TIDAK pakai trait ini (mereka global, bukan per-tenant)
- `app('currentTenant')` di-resolve oleh middleware tenancy — bind Tenant model ke container

### Tenant Resolution

```php
// apps/gym/app/Http/Middleware/ResolveTenant.php
class ResolveTenant
{
    public function handle(Request $request, Closure $next): mixed
    {
        $host = $request->getHost();
        // Extract subdomain: demo.gym.leaderhub.test → "demo"
        if (preg_match('/^(.+)\.gym\./', $host, $matches)) {
            $slug = $matches[1];
            $tenant = Tenant::where('slug', $slug)->where('status', 'active')->first();
            if (!$tenant) abort(404, 'Gym not found');
            app()->instance('currentTenant', $tenant);
        }
        return $next($request);
    }
}
```

Register middleware di `bootstrap/app.php` untuk gym app.

### Purchase Order — Receive Goods Flow

**PENTING untuk AI agent**: Action "Receive Goods" adalah custom Filament action yang:
1. Update PO status → 'received', set received_date
2. For each PO item: increment StockLevel quantity, create StockMovement (type='in')
3. Calculate landed cost: `total_idr = (subtotal_usd + shipping_usd + duty_usd) * exchange_rate`
4. All dalam DB transaction

```php
Tables\Actions\Action::make('receive')
    ->form([
        Forms\Components\TextInput::make('exchange_rate')
            ->numeric()->required()->default(15800)
            ->label('Exchange Rate (USD to IDR)'),
    ])
    ->action(function (PurchaseOrder $record, array $data) {
        DB::transaction(function () use ($record, $data) {
            $rate = $data['exchange_rate'];
            $totalUsd = $record->subtotal_usd + $record->shipping_usd + $record->duty_usd;
            $record->update([
                'exchange_rate' => $rate,
                'total_idr' => $totalUsd * $rate,
                'status' => 'received',
                'received_date' => now(),
            ]);
            foreach ($record->items as $item) {
                $stock = StockLevel::firstOrCreate(
                    ['product_id' => $item->product_id, 'warehouse_id' => $record->warehouse_id],
                    ['quantity' => 0]
                );
                $stock->increment('quantity', $item->quantity);
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $record->warehouse_id,
                    'type' => 'in',
                    'quantity' => $item->quantity,
                    'reference_type' => 'purchase_order',
                    'reference_id' => $record->id,
                ]);
            }
        });
    })
    ->visible(fn (PurchaseOrder $record) => in_array($record->status, ['sent', 'partial'])),
```

### Sales Order — Convert from Inquiry Flow

```php
Tables\Actions\Action::make('convert_inquiry')
    ->form([
        Forms\Components\Select::make('inquiry_id')
            ->options(Inquiry::where('status', 'new')->pluck('inquiry_number', 'id'))
            ->required()
            ->label('Select Inquiry'),
    ])
    ->action(function (array $data) {
        $inquiry = Inquiry::find($data['inquiry_id']);
        $so = DB::transaction(function () use ($inquiry) {
            $so = SalesOrder::create([
                'so_number' => 'SO-' . now()->year . '-' . str_pad(SalesOrder::count() + 1, 5, '0', STR_PAD_LEFT),
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
        return redirect()->to(static::getUrl('edit', ['record' => $so->id]));
    }),
```

### Installation Complete → Auto-Create Warranty

```php
Tables\Actions\Action::make('complete')
    ->action(function (Installation $record) {
        DB::transaction(function () use ($record) {
            $record->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            $so = $record->salesOrder;
            foreach ($so->items as $item) {
                Warranty::create([
                    'warranty_number' => 'WAR-' . now()->year . '-' . str_pad(Warranty::count() + 1, 5, '0', STR_PAD_LEFT),
                    'sales_order_id' => $so->id,
                    'product_id' => $item->product_id,
                    'serial_number' => $item->serial_number,
                    'customer_name' => $so->customer_name,
                    'start_date' => now(),
                    'end_date' => now()->addMonths($item->product->warranty_period_months ?? 12),
                    'status' => 'active',
                ]);
            }
        });
    })
    ->visible(fn (Installation $record) => $record->status !== 'completed'),
```

### License API

```php
// apps/hub/app/Http/Controllers/Api/LicenseController.php
public function validate(Request $request)
{
    $request->validate([
        'license_key' => 'required|string',
        'tenant_id' => 'required|integer',
    ]);

    // Rate limit: 60 per IP per minute
    $key = 'license-api:' . $request->ip();
    if (RateLimiter::tooManyAttempts($key, 60)) {
        return response()->json(['message' => 'Rate limit exceeded'], 429);
    }
    RateLimiter::hit($key, 60);

    $license = License::where('license_key', $request->license_key)->first();

    if (!$license) {
        return response()->json(['valid' => false, 'status' => 'invalid', 'message' => 'License not found'], 404);
    }

    // Auto-expire if past expiry date
    $status = $license->status;
    if ($status === 'active' && $license->expires_at->isPast()) {
        $status = 'expired';
        $license->update(['status' => 'expired']);
    }

    $valid = $status === 'active';

    // Log validation
    LicenseValidation::create([
        'license_id' => $license->id,
        'tenant_id' => $request->tenant_id,
        'valid' => $valid,
        'status' => $status,
        'validated_at' => now(),
        'ip_address' => $request->ip(),
    ]);

    return response()->json([
        'valid' => $valid,
        'status' => $status,
        'plan' => $license->plan->tier,
        'features' => $license->plan->features,
        'expires_at' => $license->expires_at->toDateString(),
        'max_members' => $license->plan->max_members,
    ]);
}
```

**Route** (`apps/hub/routes/api.php`):
```php
Route::post('/license/validate', [LicenseController::class, 'validate']);
```

**PENTING**: API ini di app `hub` (LeaderP), bukan di `gym`. LeadGym call API ini via HTTP.

### LeadGym License Validation

```php
// apps/gym/app/Services/LicenseValidatorService.php
class LicenseValidatorService
{
    public function validate(Tenant $tenant): array
    {
        $response = Http::withToken(config('services.leaderp.api_token'))
            ->post(config('services.leaderp.url') . '/api/license/validate', [
                'license_key' => $tenant->license_key,
                'tenant_id' => $tenant->id,
            ])->json();

        $tenant->update([
            'license_status' => $response['valid'] ? 'active' : $response['status'],
            'license_expires_at' => $response['expires_at'] ?? null,
        ]);

        return $response;
    }
}
```

**Config** (`apps/gym/config/services.php`):
```php
'leaderp' => [
    'url' => env('LEADERP_URL', 'https://erp.leaderhub.id'),
    'api_token' => env('LEADERP_API_TOKEN'),
],
```

**Daily Sync Command**:
```php
// apps/gym/app/Console/Commands/SyncLicenses.php
class SyncLicenses extends Command
{
    protected $signature = 'gym:sync-licenses';
    protected $description = 'Sync all tenant licenses with LeaderP';

    public function handle(): int
    {
        Tenant::whereNotNull('license_key')->chunk(100, function ($tenants) {
            foreach ($tenants as $tenant) {
                try {
                    app(LicenseValidatorService::class)->validate($tenant);
                    $this->info("Synced: {$tenant->name}");
                } catch (\Exception $e) {
                    $this->error("Failed: {$tenant->name} - {$e->getMessage()}");
                }
            }
        });
        return Command::SUCCESS;
    }
}
```

**Read-Only Mode**: Jika `tenant.license_status` bukan 'active', semua Filament resource di LeadGym disable create/edit/delete. Implementasi via Filament policy atau middleware.

## Models / Tables Affected

| Table | Action | App | Notes |
|-------|--------|-----|-------|
| `tenants` | CREATE | Gym | Multi-tenant root table |
| `gym_users` | CREATE | Gym | Per-tenant auth |
| `members` | CREATE | Gym | BelongsToTenant |
| `plans` | CREATE | Gym | BelongsToTenant |
| `subscriptions` | CREATE | Gym | BelongsToTenant |
| `services` | CREATE | Gym | BelongsToTenant |
| `member_services` | CREATE | Gym | BelongsToTenant |
| `invoices` | CREATE | Gym | BelongsToTenant, numbering per-tenant |
| `invoice_items` | CREATE | Gym | — |
| `invoice_transactions` | CREATE | Gym | — |
| `enquiries` | CREATE | Gym | BelongsToTenant |
| `follow_ups` | CREATE | Gym | BelongsToTenant |
| `expenses` | CREATE | Gym | BelongsToTenant |
| `check_ins` | CREATE | Gym | BelongsToTenant |
| `suppliers` | CREATE | Hub | — |
| `purchase_orders` | CREATE | Hub | USD currency |
| `purchase_order_items` | CREATE | Hub | — |
| `sales_orders` | CREATE | Hub | IDR currency |
| `sales_order_items` | CREATE | Hub | — |
| `sales_activities` | CREATE | Hub | — |
| `employees` | CREATE | Hub | — |
| `departments` | CREATE | Hub | Self-referencing head_id |
| `installations` | CREATE | Hub | FK technician → employees |
| `warranties` | CREATE | Hub | Auto-create on install complete |
| `warranty_claims` | CREATE | Hub | — |
| `support_tickets` | CREATE | Hub | — |
| `company_contacts` | CREATE | Hub | — |
| `agreements` | CREATE | Hub | FK → company_contacts |
| `license_customers` | CREATE | Hub | tenant_id nullable, FK added after tenants |
| `license_plans` | CREATE | Hub | Features JSON |
| `licenses` | CREATE | Hub | Auto-generate key, tenant_id nullable |
| `license_activations` | CREATE | Hub | — |
| `license_validations` | CREATE | Hub | Log table |
| `inquiries.converted_sales_order_id` | ADD FK | Website | From Phase 3, FK added now |

## Risks & Mitigations

| Risk | Impact | Mitigation |
|------|--------|------------|
| Migration order: license tables reference tenants | Migration fail (FK to non-existent table) | Run tenants migration FIRST, then license tables |
| Multi-tenant data leak | Tenant A sees Tenant B data | BelongsToTenant global scope + test cross-tenant access |
| License API down | LeadGym can't validate | Cache last validation result, allow grace period (24h) |
| Cron not running | License sync stale | Monitor via dashboard widget "Last sync" |
| Race condition on SO/inquiry conversion | Duplicate SO | DB transaction + check inquiry status before convert |

## Dependencies
- Phase 0 (monorepo, core, tenancy package)
- Phase 1 (users table, settings, Filament admin)
- Phase 2 (products, warehouses, stock_levels tables)
- Phase 3 (inquiries table — for SO conversion)