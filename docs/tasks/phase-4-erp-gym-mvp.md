# Phase 4 — LeaderP (ERP) + LeadGym MVP (Detail Task Step-by-Step)

> **Estimasi**: 3 minggu | **Tujuan**: ERP distributor + software gym multi-tenant + license manager
> **Prasyarat**: Phase 3 selesai (website lengkap, inquiry tersimpan di DB)

## Validasi Akhir Phase 4
1. LeaderP (`/hub`) dashboard menampilkan stats
2. Inventory: Product, Warehouse, Stock, Supplier CRUD
3. Purchase Order: create USD, receive → stock in, landed cost
4. Sales Order: create IDR, from inquiry, delivery → stock out
5. License Manager: issue, suspend, expire
6. License API: POST /api/license/validate berfungsi
7. LeadGym (`/gym`) multi-tenant: tenant A tidak bisa akses data tenant B
8. Member, Plan, Subscription, Invoice CRUD
9. License validation: input key → validate via API → read-only if expired
10. Dashboard kedua app menampilkan stats
11. Tidak ada error, semua FK rapi

---

## Task P4-T01: Configure Hub Filament panel

### Step-by-Step

**Step 1**: Edit `apps/hub/app/Providers/Filament/HubPanelProvider.php`
```php
public function panel(Panel $panel): Panel
{
    return $panel
        ->id('hub')
        ->path('hub')
        ->brandName('LeaderP')
        ->font('Inter')
        ->colors([
            'primary' => '#DC2626', 'danger' => '#DC2626', 'success' => '#16A34A',
            'warning' => '#D97706', 'info' => '#2563EB', 'gray' => '#64748B',
        ])
        ->renderHook('panels.head', fn () => view('filament.theme-styles'))
        ->login()
        ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
        ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
        ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
        ->navigationGroups(['Dashboard', 'ERP', 'LeadGym', 'Website', 'System']);
}
```

**Step 2**: Buat `apps/hub/resources/views/filament/theme-styles.blade.php` (sama seperti website, lihat Phase 1 T05)

### Validasi P4-T01
- [ ] `/hub/login` accessible dengan theme Modern Premium
- [ ] Login dengan admin user berhasil

---

## Task P4-T02: Migrations — ERP (suppliers, PO, SO, stock, warranty, dll)

### Step-by-Step

**Step 1**: Migration suppliers, purchase_orders, po_items
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/migrations/0002_01_04_000001_create_suppliers_table.php << 'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('country')->default('China');
            $table->string('currency', 3)->default('USD');
            $table->timestamps();
        });
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $table->date('order_date');
            $table->date('expected_date')->nullable();
            $table->date('received_date')->nullable();
            $table->string('currency', 3)->default('USD');
            $table->decimal('subtotal_usd', 12, 2)->default(0);
            $table->decimal('shipping_usd', 12, 2)->default(0);
            $table->decimal('duty_usd', 12, 2)->default(0);
            $table->decimal('total_usd', 12, 2)->default(0);
            $table->decimal('exchange_rate', 10, 2)->default(15800);
            $table->decimal('total_idr', 15, 2)->default(0);
            $table->string('status')->default('draft'); // draft, sent, partial, received, cancelled
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['supplier_id', 'status']);
            $table->index('order_date');
        });
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price_usd', 12, 2);
            $table->decimal('total_usd', 12, 2);
            $table->integer('received_quantity')->default(0);
            $table->index('purchase_order_id');
        });
    }
    public function down(): void {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('suppliers');
    }
};
PHP
```

**Step 2**: Migration sales_orders, so_items, sales_activities
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/migrations/0002_01_04_000002_create_sales_table.php << 'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('so_number')->unique();
            $table->foreignId('inquiry_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->text('customer_address')->nullable();
            $table->date('order_date');
            $table->date('delivery_date')->nullable();
            $table->decimal('subtotal_idr', 15, 2)->default(0);
            $table->decimal('discount_idr', 15, 2)->default(0);
            $table->decimal('tax_idr', 15, 2)->default(0);
            $table->decimal('total_idr', 15, 2)->default(0);
            $table->string('status')->default('draft'); // draft, confirmed, delivered, invoiced, closed, cancelled
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['status', 'order_date']);
        });
        Schema::create('sales_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price_idr', 15, 2);
            $table->decimal('total_idr', 15, 2);
            $table->string('serial_number')->nullable();
        });
        Schema::create('sales_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // lead, contact, demo, quote, order, follow_up
            $table->text('description');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('sales_activities');
        Schema::dropIfExists('sales_order_items');
        Schema::dropIfExists('sales_orders');
    }
};
PHP
```

**Step 3**: Migration installations, warranties, warranty_claims, support_tickets
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/migrations/0002_01_04_000003_create_after_sales_table.php << 'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('position')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->date('hire_date')->nullable();
            $table->decimal('salary', 15, 2)->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('head_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();
        });
        Schema::create('installations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained()->cascadeOnDelete();
            $table->date('scheduled_date');
            $table->foreignId('technician_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('location');
            $table->json('checklist')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, in_progress, completed, cancelled
            $table->timestamp('completed_at')->nullable();
            $table->string('sign_off_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        Schema::create('warranties', function (Blueprint $table) {
            $table->id();
            $table->string('warranty_number')->unique();
            $table->foreignId('sales_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('serial_number')->nullable();
            $table->string('customer_name');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('active'); // active, expired, claimed
            $table->timestamps();
            $table->index(['end_date', 'status']);
        });
        Schema::create('warranty_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warranty_id')->constrained()->cascadeOnDelete();
            $table->string('claim_number')->unique();
            $table->text('issue');
            $table->string('type')->default('repair'); // repair, replacement, refund
            $table->string('status')->default('open'); // open, in_progress, resolved, rejected
            $table->text('resolution')->nullable();
            $table->timestamps();
        });
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('subject');
            $table->text('description');
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->string('status')->default('open'); // open, in_progress, resolved, closed
            $table->foreignId('related_sales_order_id')->nullable()->constrained('sales_orders')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('warranty_claims');
        Schema::dropIfExists('warranties');
        Schema::dropIfExists('installations');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('employees');
    }
};
PHP
```

**Step 4**: Migration company_contacts, agreements
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/migrations/0002_01_04_000004_create_company_table.php << 'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('company_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // supplier, vendor, service, government
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        Schema::create('agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_contact_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('type')->default('contract'); // contract, mou, partnership
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('file_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('agreements');
        Schema::dropIfExists('company_contacts');
    }
};
PHP
```

**Step 5**: Migration license tables
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/migrations/0002_01_04_000005_create_license_table.php << 'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('license_customers', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('contact_person');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();
        });
        Schema::create('license_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tier'); // basic, pro, enterprise
            $table->decimal('price_monthly', 15, 2)->default(0);
            $table->decimal('price_yearly', 15, 2)->default(0);
            $table->json('features')->nullable();
            $table->integer('max_members')->nullable();
            $table->integer('max_staff')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('license_key', 100)->unique();
            $table->foreignId('customer_id')->constrained('license_customers')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('license_plans')->restrictOnDelete();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->string('billing_cycle')->default('monthly'); // monthly, yearly
            $table->date('started_at');
            $table->date('expires_at');
            $table->string('status')->default('active'); // active, suspended, expired, cancelled
            $table->text('suspended_reason')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['status', 'expires_at']);
        });
        Schema::create('license_activations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('tenant_id');
            $table->timestamp('activated_at');
            $table->timestamp('deactivated_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
        Schema::create('license_validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('tenant_id');
            $table->boolean('valid');
            $table->string('status');
            $table->timestamp('validated_at');
            $table->string('ip_address', 45)->nullable();
            $table->index(['license_id', 'validated_at']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('license_validations');
        Schema::dropIfExists('license_activations');
        Schema::dropIfExists('licenses');
        Schema::dropIfExists('license_plans');
        Schema::dropIfExists('license_customers');
    }
};
PHP
```

**Step 6**: Models di core (buat semua model untuk tabel di atas — pattern sama dengan P2-T01)
```bash
cd /run/media/salmon/data/Projects/leaderhub/core/src/Models
# Supplier, PurchaseOrder, PurchaseOrderItem, SalesOrder, SalesOrderItem, SalesActivity
# Employee, Department, Installation, Warranty, WarrantyClaim, SupportTicket
# CompanyContact, Agreement, LicenseCustomer, LicensePlan, License, LicenseActivation, LicenseValidation
```

Contoh `SalesOrder.php`:
```php
<?php

namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrder extends Model
{
    protected $guarded = [];
    public function inquiry(): BelongsTo { return $this->belongsTo(Inquiry::class); }
    public function items(): HasMany { return $this->hasMany(SalesOrderItem::class); }
    public function activities(): HasMany { return $this->hasMany(SalesActivity::class); }
    public function installations(): HasMany { return $this->hasMany(Installation::class); }
}
```

**Step 7**: Migration
```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/hub
php artisan migrate --path=../../core/database/migrations --force
```

### Validasi P4-T02
- [ ] Semua migration berhasil
- [ ] Semua FK ada (cek `information_schema`)
- [ ] Models accessible via tinker

---

## Task P4-T03: Inventory module (LeaderP)

### Step-by-Step

**Step 1**: Buat resources
```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/hub
php artisan make:filament-resource Product --navigation-group=ERP
php artisan make:filament-resource Warehouse --navigation-group=ERP
php artisan make:filament-resource StockLevel --navigation-group=ERP
php artisan make:filament-resource Supplier --navigation-group=ERP
php artisan make:filament-resource StockMovement --navigation-group=ERP
```

**Step 2**: Edit `ProductResource.php` (schema sama seperti website, tapi dengan stock relation manager)
```php
public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\TextInput::make('name')->required(),
        Forms\Components\TextInput::make('sku')->required()->unique(ignoreRecord: true),
        Forms\Components\Select::make('category_id')->relationship('category', 'name')->required(),
        Forms\Components\Select::make('brand_id')->relationship('brand', 'name'),
        Forms\Components\TextInput::make('purchase_price_usd')->numeric()->prefix('USD'),
        Forms\Components\TextInput::make('sale_price_idr')->numeric()->prefix('IDR'),
        Forms\Components\TextInput::make('warranty_period_months')->numeric(),
        Forms\Components\Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])->required(),
    ]);
}

public static function getRelations(): array
{
    return [
        RelationManagers\StockLevelsRelationManager::class,
    ];
}
```

**Step 3**: Buat `StockLevelsRelationManager`
```bash
php artisan make:filament-relation-manager Product stockLevels warehouse_id
```

### Validasi P4-T03
- [ ] CRUD Product, Warehouse, Supplier berfungsi
- [ ] Stock level per warehouse bisa di-set dari Product detail
- [ ] Stock movement ter-log saat stock berubah

---

## Task P4-T04: Purchase module

### Step-by-Step

**Step 1**: Buat `PurchaseOrderResource`
```bash
php artisan make:filament-resource PurchaseOrder
```

**Step 2**: Form dengan items repeater + receive action
```php
public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\TextInput::make('po_number')->required()->unique(ignoreRecord: true)->default(fn () => 'PO-' . now()->year . '-' . str_pad(PurchaseOrder::whereYear('created_at', now()->year)->count() + 1, 5, '0', STR_PAD_LEFT)),
        Forms\Components\Select::make('supplier_id')->relationship('supplier', 'name')->required(),
        Forms\Components\Select::make('warehouse_id')->relationship('warehouse', 'name')->required(),
        Forms\Components\DatePicker::make('order_date')->required()->default(now()),
        Forms\Components\DatePicker::make('expected_date'),
        Forms\Components\Repeater::make('items')->relationship()->schema([
            Forms\Components\Select::make('product_id')->relationship('product', 'name')->required(),
            Forms\Components\TextInput::make('quantity')->numeric()->required(),
            Forms\Components\TextInput::make('unit_price_usd')->numeric()->required()->prefix('USD'),
        ])->columnSpanFull(),
        Forms\Components\TextInput::make('shipping_usd')->numeric()->prefix('USD')->default(0),
        Forms\Components\TextInput::make('duty_usd')->numeric()->prefix('USD')->default(0),
        Forms\Components\TextInput::make('exchange_rate')->numeric()->default(15800),
        Forms\Components\Select::make('status')->options(['draft' => 'Draft', 'sent' => 'Sent', 'partial' => 'Partial', 'received' => 'Received', 'cancelled' => 'Cancelled'])->required()->default('draft'),
    ]);
}
```

**Step 3**: Action "Receive Goods" (custom action di table)
```php
Tables\Actions\Action::make('receive')
    ->form([
        Forms\Components\TextInput::make('exchange_rate')->numeric()->required()->default(15800),
    ])
    ->action(function (PurchaseOrder $record, array $data) {
        // Update stock, create stock movements, calc landed cost, update PO status
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
    }),
```

### Validasi P4-T04
- [ ] Create PO dengan items USD berhasil
- [ ] Receive goods → stock naik, stock movement ter-log
- [ ] Landed cost (total_idr) terhitung

---

## Task P4-T05: Sales module

### Step-by-Step

**Step 1**: Buat `SalesOrderResource`
```bash
php artisan make:filament-resource SalesOrder
```

**Step 2**: Form + action "Convert from Inquiry" + "Deliver"
```php
// Form sama seperti PO tapi IDR
// Action convert from inquiry:
Tables\Actions\Action::make('convert_inquiry')
    ->form([Forms\Components\Select::make('inquiry_id')->options(Inquiry::where('status', 'new')->pluck('inquiry_number', 'id'))->required()])
    ->action(function (array $data) {
        $inquiry = Inquiry::find($data['inquiry_id']);
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
        $inquiry->update(['status' => 'converted', 'converted_sales_order_id' => $so->id]);
    }),
```

### Validasi P4-T05
- [ ] Create SO langsung berhasil
- [ ] Convert dari inquiry berhasil (inquiry status → converted)
- [ ] Deliver → stock turun, stock movement ter-log

---

## Task P4-T06: Installation, Warranty, After-sales

### Step-by-Step

**Step 1**: Buat resources
```bash
php artisan make:filament-resource Installation
php artisan make:filament-resource Warranty
php artisan make:filament-resource WarrantyClaim
php artisan make:filament-resource SupportTicket
```

**Step 2**: Action "Complete Installation" → auto-create warranty
```php
Tables\Actions\Action::make('complete')
    ->action(function (Installation $record) {
        $record->update(['status' => 'completed', 'completed_at' => now()]);
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
    }),
```

### Validasi P4-T06
- [ ] Schedule installation berhasil
- [   ] Complete installation → warranty auto-create
- [ ] Warranty claim bisa dibuat
- [ ] Support ticket CRUD berfungsi

---

## Task P4-T07: License Manager module

### Step-by-Step

**Step 1**: Buat resources
```bash
php artisan make:filament-resource LicenseCustomer
php artisan make:filament-resource LicensePlan
php artisan make:filament-resource License
```

**Step 2**: License form dengan auto-generate key
```php
Forms\Components\TextInput::make('license_key')->disabled()->default(fn () => 'LDR-GYM-' . strtoupper(\Str::random(16))),
```

**Step 3**: Action "Suspend" dan "Renew"
```php
Tables\Actions\Action::make('suspend')->form([Forms\Components\Textarea::make('reason')])->action(fn (License $record, array $data) => $record->update(['status' => 'suspended', 'suspended_reason' => $data['reason']])),
Tables\Actions\Action::make('renew')->form([Forms\Components\DatePicker::make('expires_at')->required()])->action(fn (License $record, array $data) => $record->update(['status' => 'active', 'expires_at' => $data['expires_at']])),
```

### Validasi P4-T07
- [ ] Create customer, plan, license berhasil
- [   ] License key auto-generate
- [ ] Suspend & renew berfungsi

---

## Task P4-T08: License API

### Step-by-Step

**Step 1**: Install Sanctum
```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/hub
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

**Step 2**: Buat API controller
```bash
php artisan make:controller Api/LicenseController
```

Edit:
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Leaderhub\Core\Models\License;
use Leaderhub\Core\Models\LicenseValidation;

class LicenseController extends Controller
{
    public function validate(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string',
            'tenant_id' => 'required|integer',
        ]);

        $key = 'license-api:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 60)) {
            return response()->json(['message' => 'Rate limit exceeded'], 429);
        }
        RateLimiter::hit($key, 60);

        $license = License::where('license_key', $request->license_key)->first();

        if (!$license) {
            return response()->json(['valid' => false, 'status' => 'invalid', 'message' => 'License not found'], 404);
        }

        $valid = $license->status === 'active' && $license->expires_at->isFuture();
        $status = $license->status;
        if ($status === 'active' && $license->expires_at->isPast()) {
            $status = 'expired';
            $license->update(['status' => 'expired']);
        }

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
}
```

**Step 3**: Routes `routes/api.php`
```php
Route::post('/license/validate', [LicenseController::class, 'validate']);
```

### Validasi P4-T08
- [ ] `POST /api/license/validate` dengan key valid → return valid:true
- [ ] Dengan key expired → return valid:false
- [ ] Rate limit 60/min berfungsi

---

## Task P4-T09: Dashboard widgets (LeaderP)

### Step-by-Step

**Step 1**: Buat widgets
```bash
php artisan make:filament-widget StatsOverview --type=stats
```

Edit `app/Filament/Widgets/StatsOverview.php`:
```php
public function getStats(): array
{
    return [
        Stat::make('New Inquiries', Inquiry::where('status', 'new')->count())->description('This week'),
        Stat::make('Low Stock Products', Product::whereHas('stockLevels', fn ($q) => $q->whereRaw('quantity < low_stock_threshold'))->count())->description('Need restock'),
        Stat::make('Active Licenses', License::where('status', 'active')->count())->description('LeadGym'),
        Stat::make('Expiring Licenses', License::where('status', 'active')->where('expires_at', '<', now()->addDays(7))->count())->description('7 days'),
    ];
}
```

### Validasi P4-T09
- [ ] Dashboard menampilkan 4 stat cards
- [   ] Angka akurat

---

## Task P4-T10: Role & permission (Shield)

### Step-by-Step

```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/hub
php artisan shield:install
php artisan shield:generate --all
```

Setup roles di seeder:
```php
$roles = ['super_admin', 'content_admin', 'sales_admin', 'inventory_admin', 'accounting_admin', 'hr_admin', 'company_admin', 'license_admin'];
foreach ($roles as $role) {
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role]);
}
```

### Validasi P4-T10
- [ ] Role & permission ter-generate
- [   ] User dengan role `inventory_admin` hanya bisa akses inventory modul

---

## Task P4-T11: Configure stancl/tenancy + Gym Filament panel

### Step-by-Step

**Step 1**: Configure tenancy di `apps/gym/config/tenancy.php`
```php
'database' => [
    'central_connection' => env('DB_CONNECTION', 'mysql'),
    'tenant_connection' => env('DB_CONNECTION', 'mysql'),
],
'identification' => ['mode' => 'subdomain'],
```

**Step 2**: Buat Tenant model di core
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/src/Models/Tenant.php << 'PHP'
<?php

namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $guarded = [];
    protected $casts = ['operating_hours' => 'array', 'license_expires_at' => 'date'];
}
PHP
```

**Step 3**: Buat BelongsToTenant trait
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/src/Traits/BelongsToTenant.php << 'PHP'
<?php

namespace Leaderhub\Core\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if ($tenantId = app('currentTenant')?->id) {
                $builder->where('tenant_id', $tenantId);
            }
        });

        static::creating(function (Model $model) {
            if ($tenantId = app('currentTenant')?->id) {
                $model->tenant_id = $tenantId;
            }
        });
    }
}
PHP
```

**Step 4**: Edit `apps/gym/app/Providers/Filament/GymPanelProvider.php`
```php
public function panel(Panel $panel): Panel
{
    return $panel
        ->id('gym')
        ->path('gym')
        ->brandName(fn () => app('currentTenant')?->name ?? 'LeadGym')
        ->font('Inter')
        ->colors(['primary' => '#DC2626', ...])
        ->renderHook('panels.head', fn () => view('filament.theme-styles'))
        ->login()
        ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
        ->navigationGroups(['Dashboard', 'Gym', 'System']);
}
```

### Validasi P4-T11
- [ ] Tenant subdomain resolve (`demo.gym.leaderhub.test`)
- [   ] Panel load per-tenant

---

## Task P4-T12: Migrations — LeadGym (tenants, gym_users, members, plans, subs, invoices, dll)

### Step-by-Step

**Step 1**: Migration tenants, gym_users
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/migrations/0002_01_04_000006_create_tenants_table.php << 'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain')->nullable();
            $table->string('logo')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('primary_color')->nullable();
            $table->integer('capacity')->default(50);
            $table->json('operating_hours')->nullable();
            $table->unsignedBigInteger('license_id')->nullable();
            $table->string('license_key')->nullable();
            $table->string('license_status')->default('inactive');
            $table->date('license_expires_at')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
        Schema::create('gym_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->string('role')->default('gym_staff'); // gym_owner, gym_staff, gym_trainer
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['tenant_id', 'email']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('gym_users');
        Schema::dropIfExists('tenants');
    }
};
PHP
```

**Step 2**: Migration members, plans, subscriptions, services, member_services
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/migrations/0002_01_04_000007_create_gym_core_table.php << 'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('member_code');
            $table->string('name');
            $table->string('photo')->nullable();
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->string('qr_code')->nullable();
            $table->string('status')->default('active');
            $table->date('joined_at');
            $table->timestamps();
            $table->unique(['tenant_id', 'member_code']);
            $table->index(['tenant_id', 'status']);
        });
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->default('membership'); // membership, pt_sessions, add_on
            $table->integer('duration_days');
            $table->decimal('price', 15, 2);
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('tenant_id');
        });
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('active'); // active, expired, frozen, transferred, cancelled
            $table->date('frozen_at')->nullable();
            $table->integer('frozen_days')->default(0);
            $table->unsignedBigInteger('transferred_to')->nullable();
            $table->decimal('amount', 15, 2);
            $table->timestamps();
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'end_date']);
        });
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->default('pt_session'); // pt_session, class, locker, towel, other
            $table->decimal('price', 15, 2);
            $table->integer('duration_minutes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('member_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->integer('total_sessions');
            $table->integer('used_sessions')->default(0);
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('member_services');
        Schema::dropIfExists('services');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
        Schema::dropIfExists('members');
    }
};
PHP
```

**Step 3**: Migration invoices, invoice_items, invoice_transactions, enquiries, follow_ups, expenses, check_ins
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/migrations/0002_01_04_000008_create_gym_ops_table.php << 'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number');
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->string('status')->default('unpaid'); // unpaid, partial, paid, cancelled
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('gym_users')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['tenant_id', 'invoice_number']);
        });
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->string('type')->default('subscription'); // subscription, service, add_on, penalty
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total', 15, 2);
        });
        Schema::create('invoice_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->default('cash'); // cash, transfer, card, qris, other
            $table->date('payment_date');
            $table->string('reference')->nullable();
            $table->foreignId('created_by')->constrained('gym_users')->restrictOnDelete();
            $table->timestamps();
        });
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('source')->default('walk_in'); // walk_in, online, referral, social
            $table->string('interest')->nullable();
            $table->string('status')->default('new'); // new, contacted, converted, lost
            $table->unsignedBigInteger('converted_member_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enquiry_id')->constrained()->cascadeOnDelete();
            $table->date('follow_up_date');
            $table->text('note');
            $table->string('next_action')->nullable();
            $table->string('status')->default('pending'); // pending, done
            $table->foreignId('created_by')->constrained('gym_users')->restrictOnDelete();
            $table->timestamps();
        });
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->date('expense_date');
            $table->string('category');
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('gym_users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['tenant_id', 'expense_date']);
        });
        Schema::create('check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('check_in_at');
            $table->timestamp('check_out_at')->nullable();
            $table->foreignId('created_by')->constrained('gym_users')->restrictOnDelete();
            $table->index(['tenant_id', 'check_in_at']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('check_ins');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('follow_ups');
        Schema::dropIfExists('enquiries');
        Schema::dropIfExists('invoice_transactions');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
PHP
```

**Step 4**: Models di core dengan BelongsToTenant trait
```bash
cd /run/media/salmon/data/Projects/leaderhub/core/src/Models
# Member, Plan, Subscription, Service, MemberService, Invoice, InvoiceItem, InvoiceTransaction
# Enquiry, FollowUp, Expense, CheckIn, GymUser
```

Contoh `Member.php`:
```php
<?php

namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Leaderhub\Core\Traits\BelongsToTenant;

class Member extends Model
{
    use BelongsToTenant;
    protected $guarded = [];
    public function subscriptions() { return $this->hasMany(Subscription::class); }
}
```

**Step 5**: Migration
```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/gym
php artisan migrate --path=../../core/database/migrations --force
```

### Validasi P4-T12
- [ ] Semua migration berhasil
- [   ] Semua FK ada
- [ ] Models accessible via tinker dengan tenant scope

---

## Task P4-T13: Member module (LeadGym)

### Step-by-Step

**Step 1**: Buat resource
```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/gym
php artisan make:filament-resource Member
```

**Step 2**: Form dengan QR code generation
```php
Forms\Components\TextInput::make('member_code')->required()->default(fn () => 'M-' . str_pad(Member::count() + 1, 4, '0', STR_PAD_LEFT)),
Forms\Components\TextInput::make('name')->required(),
Forms\Components\FileUpload::make('photo')->image()->directory('members'),
Forms\Components\Select::make('gender')->options(['male' => 'Male', 'female' => 'Female']),
Forms\Components\DatePicker::make('dob'),
Forms\Components\TextInput::make('phone'),
Forms\Components\TextInput::make('email'),
Forms\Components\Textarea::make('address'),
Forms\Components\TextInput::make('emergency_contact'),
Forms\Components\TextInput::make('emergency_phone'),
Forms\Components\DatePicker::make('joined_at')->required()->default(now()),
```

### Validasi P4-T13
- [ ] CRUD member berfungsi
- [   ] Member hanya tampil untuk tenant saat ini (no cross-tenant)
- [ ] QR code ter-generate

---

## Task P4-T14: Plan & Subscription module

### Step-by-Step

**Step 1**: Buat `PlanResource` dan `SubscriptionResource`
```bash
php artisan make:filament-resource Plan
php artisan make:filament-resource Subscription
```

**Step 2**: Subscription form dengan auto-calc end_date
```php
Forms\Components\Select::make('member_id')->relationship('member', 'name')->required(),
Forms\Components\Select::make('plan_id')->relationship('plan', 'name')->required()->live(),
Forms\Components\DatePicker::make('start_date')->required()->default(now())->live(),
Forms\Components\DatePicker::make('end_date')->disabled()->default(fn ($get) => $get('start_date') && $get('plan_id') ? \Carbon\Carbon::parse($get('start_date'))->addDays(Plan::find($get('plan_id'))->duration_days) : null),
```

### Validasi P4-T14
- [ ] CRUD plan berfungsi
- [   ] Create subscription → end_date auto-calc
- [ ] Renewal, freeze, transfer berfungsi

---

## Task P4-T15: Invoice module

### Step-by-Step

**Step 1**: Buat `InvoiceResource`
```bash
php artisan make:filament-resource Invoice
```

**Step 2**: Form dengan items + payment
```php
Forms\Components\TextInput::make('invoice_number')->disabled()->default(fn () => 'INV-' . str_pad(Invoice::count() + 1, 5, '0', STR_PAD_LEFT)),
Forms\Components\Select::make('member_id')->relationship('member', 'name'),
Forms\Components\DatePicker::make('issue_date')->required()->default(now()),
Forms\Components\Repeater::make('items')->relationship()->schema([
    Forms\Components\TextInput::make('description')->required(),
    Forms\Components\TextInput::make('quantity')->numeric()->default(1),
    Forms\Components\TextInput::make('unit_price')->numeric()->required(),
])->columnSpanFull(),
```

### Validasi P4-T15
- [ ] Create invoice dengan items berhasil
- [   ] Payment record bisa ditambah
- [ ] PDF generation berfungsi

---

## Task P4-T16: Service, Enquiry, Expense, Check-in modules

### Step-by-Step

**Step 1**: Buat resources
```bash
php artisan make:filament-resource Service
php artisan make:filament-resource Enquiry
php artisan make:filament-resource Expense
php artisan make:filament-resource CheckIn
```

**Step 2**: Check-in dengan subscription validation
```php
// CheckIn form
Forms\Components\Select::make('member_id')->relationship('member', 'name')->required(),
// Action: validate active subscription sebelum check-in
```

### Validasi P4-T16
- [ ] Service CRUD berfungsi
- [   ] Enquiry & follow-up berfungsi
- [ ] Expense CRUD berfungsi
- [ ] Check-in validate subscription (block if expired)

---

## Task P4-T17: Dashboard (LeadGym)

### Step-by-Step

```bash
php artisan make:filament-widget GymStats --type=stats
```

```php
public function getStats(): array
{
    $tenantId = app('currentTenant')?->id;
    return [
        Stat::make('Active Members', Member::where('tenant_id', $tenantId)->where('status', 'active')->count()),
        Stat::make('Revenue This Month', 'Rp ' . number_format(Invoice::where('tenant_id', $tenantId)->whereMonth('created_at', now()->month)->sum('total'))),
        Stat::make('Check-in Today', CheckIn::where('tenant_id', $tenantId)->whereDate('check_in_at', today())->count()),
        Stat::make('Expiring 7 days', Subscription::where('tenant_id', $tenantId)->where('status', 'active')->where('end_date', '<', now()->addDays(7))->count()),
    ];
}
```

### Validasi P4-T17
- [ ] Dashboard menampilkan 4 stat cards per-tenant

---

## Task P4-T18: Tenant Settings & License validation

### Step-by-Step

**Step 1**: Buat `TenantSettingResource` (singleton per tenant)
**Step 2**: Buat `LicenseValidatorService`
```bash
cat > /run/media/salmon/data/Projects/leaderhub/apps/gym/app/Services/LicenseValidatorService.php << 'PHP'
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Leaderhub\Core\Models\Tenant;

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
PHP
```

**Step 3**: Buat cron command `SyncLicenses`
```bash
php artisan make:command SyncLicenses
```

Edit:
```php
protected $signature = 'gym:sync-licenses';
public function handle()
{
    Tenant::whereNotNull('license_key')->chunk(100, function ($tenants) {
        foreach ($tenants as $tenant) {
            app(LicenseValidatorService::class)->validate($tenant);
        }
    });
}
```

### Validasi P4-T18
- [ ] Input license key di settings → validate via API
- [   ] Status update di tenant
- [ ] Cron `gym:sync-licenses` berjalan
- [ ] Read-only mode if expired

---

## Task P4-T19: Role & permission per-tenant (Shield)

### Step-by-Step

```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/gym
php artisan shield:install
php artisan shield:generate --all
```

### Validasi P4-T19
- [ ] Role gym_owner, gym_staff, gym_trainer ter-generate
- [   ] Permission scoped per-tenant

---

## Task P4-T20: Customize UI sesuai design system

### Step-by-Step

Buat `apps/gym/resources/views/filament/theme-styles.blade.php` (sama seperti hub, lihat P4-T01 Step 2)

### Validasi P4-T20
- [ ] UI LeadGym konsisten dengan LeaderP & website (Modern Premium)

---

## Checklist Akhir Phase 4

```bash
# LeaderP
cd /run/media/salmon/data/Projects/leaderhub/apps/hub
php artisan migrate:status | grep Pending && echo "✗ Pending" || echo "✓ Migrations OK"
php artisan serve --port=8001 &
sleep 2
curl -s http://localhost:8001/hub/login | grep -q "Filament" && echo "✓ Hub OK" || echo "✗ Hub FAIL"
kill %1

# License API
curl -s -X POST http://localhost:8001/api/license/validate -H "Content-Type: application/json" -d '{"license_key":"test","tenant_id":1}' | grep -q "valid" && echo "✓ API OK" || echo "✗ API FAIL"

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
use Leaderhub\Core\Models\Member;
use Leaderhub\Core\Models\License;
echo 'Tenants: ' . Tenant::count() . PHP_EOL;
echo 'Licenses: ' . License::count() . PHP_EOL;
"

cd /run/media/salmon/data/Projects/leaderhub
git add -A && git commit -m "Phase 4: LeaderP ERP + LeadGym MVP + License Manager"
```

**Jika semua ✓ → lanjut Phase 5.**