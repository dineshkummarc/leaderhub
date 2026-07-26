# Phase 0 — Monorepo Setup (Detail Task Step-by-Step)

> **Estimasi**: 2 hari | **Tujuan**: Struktur monorepo + core package + design tokens + 3 app skeleton yang bisa jalan tanpa error
> **Prasyarat**: PHP 8.2+, Composer 2.x, Node 20+, pnpm 9.x, MySQL 8.0/MariaDB 10.6+

## Validasi Akhir Phase 0
Sebelum phase dinyatakan selesai, semua ini HARUS lolos:
1. `cd apps/website && php artisan serve` → load di `http://localhost:8000`
2. `cd apps/hub && php artisan serve` → load di `http://localhost:8001`
3. `cd apps/gym && php artisan serve` → load di `http://localhost:8002`
4. `php artisan tinker` di tiap app → `Leaderhub\Core\Models\Test::all()` berfungsi
5. `cd packages/design-tokens && pnpm build` → `dist/` terisi
6. `pnpm install` di root berhasil
7. `git status` clean, semua sudah commit
8. Tidak ada error PHP, migration, atau autoload

---

## Task P0-T01: Inisialisasi struktur folder & git

### Tujuan
Buat root folder, init git, file konfigurasi root.

### Step-by-Step

**Step 1**: Buat folder root & init git
```bash
mkdir -p /run/media/salmon/data/Projects/leaderhub
cd /run/media/salmon/data/Projects/leaderhub
git init
git branch -M main
```

**Step 2**: Buat `.gitignore`
```bash
cat > .gitignore << 'EOF'
# Dependencies
node_modules/
vendor/
packages/*/node_modules/
apps/*/vendor/
apps/*/node_modules/

# Build artifacts (di-commit untuk cPanel deploy, tapi dev artifacts di-ignore)
*.log
.DS_Store

# Environment
.env
.env.local
.env.*.local
apps/*/.env
apps/*/.env.local

# Storage & cache (keep structure via .gitkeep)
storage/app/*
storage/framework/cache/*
storage/framework/sessions/*
storage/framework/views/*
storage/logs/*
!**/.gitkeep

# IDE
.idea/
.vscode/
*.swp

# OS
Thumbs.db

# Temp
.tmp/
.opencode/
EOF
```

**Step 3**: Buat root `composer.json` (path repositories)
```json
{
  "name": "leaderhub/leaderhub",
  "description": "LeaderHub monorepo — website + ERP + gym software",
  "type": "project",
  "license": "proprietary",
  "repositories": [
    { "type": "path", "url": "./core" },
    { "type": "path", "url": "./packages/design-tokens" }
  ],
  "require": {
    "php": "^8.2"
  },
  "config": {
    "optimize-autoloader": true,
    "preferred-install": "dist",
    "sort-packages": true
  },
  "minimum-stability": "dev",
  "prefer-stable": true
}
```

**Step 4**: Buat root `package.json` (pnpm workspace)
```json
{
  "name": "leaderhub",
  "private": true,
  "version": "1.0.0",
  "scripts": {
    "dev:website": "pnpm --filter ./apps/website dev",
    "dev:hub": "pnpm --filter ./apps/hub dev",
    "dev:gym": "pnpm --filter ./apps/gym dev",
    "build:tokens": "pnpm --filter @leaderhub/design-tokens build",
    "build:website": "pnpm --filter ./apps/website build",
    "build:hub": "pnpm --filter ./apps/hub build",
    "build:gym": "pnpm --filter ./apps/gym build",
    "build:all": "pnpm -r --filter './apps/*' build"
  },
  "packageManager": "pnpm@9.0.0",
  "engines": { "node": ">=20" }
}
```

**Step 5**: Buat `pnpm-workspace.yaml`
```yaml
packages:
  - 'packages/*'
  - 'apps/website'
  - 'apps/hub'
  - 'apps/gym'
```

**Step 6**: Buat root `README.md`
```markdown
# LeaderHub

Platform terintegrasi untuk distributor alat fitness — website pemasaran, ERP (LeaderP), dan software gym multi-tenant (LeadGym) dalam satu monorepo.

## Struktur
- `core/` — shared package (models, services, migrations)
- `packages/design-tokens/` — design tokens (sumber tunggal)
- `apps/website/` — website publik (Laravel 12 + Vue + Filament)
- `apps/hub/` — LeaderP ERP (Laravel 12 + Filament 5)
- `apps/gym/` — LeadGym (Laravel 12 + Filament 5, multi-tenant)

## Quick Start
Lihat `docs/06-deployment.md` bagian Dev Environment.
```

**Step 7**: Buat folder structure dasar
```bash
mkdir -p core/src/{Models,Services,Enums,Traits,Contracts}
mkdir -p core/database/{migrations,seeders,factories}
mkdir -p packages/design-tokens/dist
mkdir -p apps storage/app/public
```

**Step 8**: Buat `.gitkeep` agar folder kosong ter-track
```bash
touch core/src/Models/.gitkeep core/src/Services/.gitkeep core/src/Enums/.gitkeep
touch core/src/Traits/.gitkeep core/src/Contracts/.gitkeep
touch core/database/migrations/.gitkeep core/database/seeders/.gitkeep core/database/factories/.gitkeep
touch storage/app/public/.gitkeep
```

### Validasi P0-T01
- [ ] `git status` menunjukkan file baru (untracked)
- [ ] `cat composer.json` valid JSON
- [ ] `cat package.json` valid JSON
- [ ] Folder `core/`, `packages/`, `apps/`, `storage/` ada

---

## Task P0-T02: Setup core package

### Tujuan
Buat `core/` sebagai composer package dengan PSR-4 autoload `Leaderhub\Core\`.

### Step-by-Step

**Step 1**: Buat `core/composer.json`
```json
{
  "name": "leaderhub/core",
  "description": "LeaderHub shared core — models, services, migrations",
  "type": "library",
  "license": "proprietary",
  "autoload": {
    "psr-4": {
      "Leaderhub\\Core\\": "src/"
    }
  },
  "autoload-dev": {
    "psr-4": {
      "Leaderhub\\Core\\Tests\\": "tests/"
    }
  },
  "require": {
    "php": "^8.2",
    "illuminate/database": "^12.0",
    "illuminate/support": "^12.0"
  },
  "extra": {
    "laravel": {
      "providers": [
        "Leaderhub\\Core\\CoreServiceProvider"
      ]
    }
  },
  "minimum-stability": "dev",
  "prefer-stable": true
}
```

**Step 2**: Buat `core/src/CoreServiceProvider.php`
```php
<?php

namespace Leaderhub\Core;

use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register bindings, configs
    }

    public function boot(): void
    {
        // Load migrations dari core (opsional — bisa juga via --path)
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
```

**Step 3**: Buat test model sementara (untuk validasi akses dari app)
```bash
cat > core/src/Models/Test.php << 'PHP'
<?php

namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $table = 'tests';
    protected $guarded = [];
}
PHP
```

**Step 4**: Buat migration test
```bash
cat > core/database/migrations/0001_01_01_000001_create_tests_table.php << 'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
PHP
```

**Step 5**: Install dependencies core
```bash
cd /run/media/salmon/data/Projects/leaderhub/core
composer install
```

### Validasi P0-T02
- [ ] `cd core && composer install` berhasil tanpa error
- [ ] `php -r "require 'vendor/autoload.php'; echo Leaderhub\Core\Models\Test::class;"` output `Leaderhub\Core\Models\Test`
- [ ] File `core/src/CoreServiceProvider.php` ada

---

## Task P0-T03: Setup design-tokens package

### Tujuan
Buat `packages/design-tokens/` dengan tokens.json (sumber), build script, dist output.

### Step-by-Step

**Step 1**: Buat `packages/design-tokens/package.json`
```json
{
  "name": "@leaderhub/design-tokens",
  "version": "1.0.0",
  "private": true,
  "description": "LeaderHub design tokens — sumber tunggal untuk semua app",
  "scripts": {
    "build": "node build.js"
  },
  "devDependencies": {
    "style-dictionary": "^4.0.0"
  }
}
```

**Step 2**: Buat `packages/design-tokens/tokens.json` (sumber kebenaran, sesuai design system v1.1)
```json
{
  "color": {
    "light": {
      "bg": { "value": "#FFFFFF", "type": "color" },
      "surface": { "value": "#F8FAFC", "type": "color" },
      "surface_2": { "value": "#F1F5F9", "type": "color" },
      "elevated": { "value": "#FFFFFF", "type": "color" },
      "text": { "value": "#0F172A", "type": "color" },
      "text_muted": { "value": "#64748B", "type": "color" },
      "text_subtle": { "value": "#94A3B8", "type": "color" },
      "border": { "value": "#E2E8F0", "type": "color" },
      "border_strong": { "value": "#CBD5E1", "type": "color" },
      "accent": { "value": "#DC2626", "type": "color" },
      "accent_hover": { "value": "#B91C1C", "type": "color" },
      "accent_soft": { "value": "#FEF2F2", "type": "color" },
      "success": { "value": "#16A34A", "type": "color" },
      "warning": { "value": "#D97706", "type": "color" },
      "info": { "value": "#2563EB", "type": "color" }
    },
    "dark": {
      "bg": { "value": "#0B0F14", "type": "color" },
      "surface": { "value": "#111821", "type": "color" },
      "surface_2": { "value": "#1A2330", "type": "color" },
      "elevated": { "value": "#131B26", "type": "color" },
      "text": { "value": "#F1F5F9", "type": "color" },
      "text_muted": { "value": "#94A3B8", "type": "color" },
      "text_subtle": { "value": "#64748B", "type": "color" },
      "border": { "value": "#1F2937", "type": "color" },
      "border_strong": { "value": "#334155", "type": "color" },
      "accent": { "value": "#EF4444", "type": "color" },
      "accent_hover": { "value": "#F87171", "type": "color" },
      "accent_soft": { "value": "rgba(239,68,68,0.10)", "type": "color" },
      "success": { "value": "#16A34A", "type": "color" },
      "warning": { "value": "#D97706", "type": "color" },
      "info": { "value": "#2563EB", "type": "color" }
    }
  },
  "radius": {
    "sm": { "value": "6px", "type": "dimension" },
    "default": { "value": "10px", "type": "dimension" },
    "lg": { "value": "14px", "type": "dimension" },
    "xl": { "value": "18px", "type": "dimension" },
    "full": { "value": "9999px", "type": "dimension" }
  },
  "font": {
    "sans": { "value": "Inter, system-ui, sans-serif", "type": "fontFamily" },
    "mono": { "value": "ui-monospace, SF Mono, Menlo, monospace", "type": "fontFamily" }
  }
}
```

**Step 3**: Buat `packages/design-tokens/build.js`
```js
const StyleDictionary = require('style-dictionary');
const fs = require('fs');
const path = require('path');

const tokens = require('./tokens.json');

// Register format untuk Tailwind config
StyleDictionary.registerFormat({
  name: 'js/tailwind',
  formatter: ({ dictionary }) => {
    const colors = {};
    dictionary.allTokens.filter(t => t.type === 'color').forEach(t => {
      // path: color.light.bg -> colors.bg (light), color.dark.bg -> colors.bg (dark handled via CSS var)
      const parts = t.path;
      if (parts[1] === 'light') {
        const name = parts[2].replace(/_/g, '-');
        colors[name] = `var(--color-${name})`;
      }
    });
    return `module.exports = ${JSON.stringify({
      colors,
      fontFamily: {
        sans: [tokens.font.sans.value],
        mono: [tokens.font.mono.value],
      },
      borderRadius: {
        sm: tokens.radius.sm.value,
        DEFAULT: tokens.radius.default.value,
        lg: tokens.radius.lg.value,
        xl: tokens.radius.xl.value,
        full: tokens.radius.full.value,
      },
    }, null, 2)};\n`;
  },
});

// Register format untuk Filament theme PHP
StyleDictionary.registerFormat({
  name: 'php/filament',
  formatter: ({ dictionary }) => {
    const accent = dictionary.allTokens.find(t => t.path.join('.') === 'color.light.accent').value;
    const success = dictionary.allTokens.find(t => t.path.join('.') === 'color.light.success').value;
    const warning = dictionary.allTokens.find(t => t.path.join('.') === 'color.light.warning').value;
    const info = dictionary.allTokens.find(t => t.path.join('.') === 'color.light.info').value;
    return `<?php\n\nreturn [\n  'primary' => '${accent}',\n  'danger' => '${accent}',\n  'success' => '${success}',\n  'warning' => '${warning}',\n  'info' => '${info}',\n  'gray' => '#64748B',\n];\n`;
  },
});

StyleDictionary.extend({
  source: ['tokens.json'],
  platforms: {
    css: {
      transformGroup: 'css',
      buildPath: 'dist/',
      files: [{
        destination: 'tokens.css',
        format: 'css/variables',
        options: { outputReferences: true },
      }],
    },
    tailwind: {
      buildPath: 'dist/',
      files: [{
        destination: 'tokens.js',
        format: 'js/tailwind',
      }],
    },
    filament: {
      buildPath: 'dist/',
      files: [{
        destination: 'filament-theme.php',
        format: 'php/filament',
      }],
    },
  },
}).buildAllPlatforms();

// Tambahkan dark mode variables ke tokens.css (append manual)
const cssPath = path.join(__dirname, 'dist/tokens.css');
let css = fs.readFileSync(cssPath, 'utf8');
const darkVars = [];
Object.entries(tokens.color.dark).forEach(([key, val]) => {
  const name = key.replace(/_/g, '-');
  darkVars.push(`  --color-${name}: ${val.value};`);
});
css = css.replace(':root {', `:root {\n  /* Light (default) */`);
css += `\nhtml.dark {\n${darkVars.join('\n')}\n}\n`;
fs.writeFileSync(cssPath, css);
console.log('✓ Design tokens built: tokens.css, tokens.js, filament-theme.php');
```

**Step 4**: Install & build
```bash
cd /run/media/salmon/data/Projects/leaderhub/packages/design-tokens
pnpm install
pnpm build
```

**Step 5**: Verifikasi output
```bash
ls dist/
# Harus ada: tokens.css, tokens.js, filament-theme.php
```

### Validasi P0-T03
- [ ] `pnpm install` berhasil
- [ ] `pnpm build` menghasilkan `dist/tokens.css`, `dist/tokens.js`, `dist/filament-theme.php`
- [ ] `dist/tokens.css` berisi `:root { --color-bg: #FFFFFF; ... }` dan `html.dark { --color-bg: #0B0F14; ... }`
- [ ] `dist/tokens.js` berisi `module.exports = { colors: {...}, ... }`

---

## Task P0-T04: Setup apps/website (Laravel 12 + Filament 5)

### Tujuan
Fresh install Laravel 12, install Filament 5, require core + design-tokens.

### Step-by-Step

**Step 1**: Install Laravel 12
```bash
cd /run/media/salmon/data/Projects/leaderhub/apps
composer create-project laravel/laravel website
cd website
```

**Step 2**: Konfigurasi `.env` (database shared)
```bash
# Edit apps/website/.env
APP_NAME="LeaderHub"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=leaderhub_db
DB_USERNAME=root
DB_PASSWORD=   # sesuaikan

# Cache & session
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

**Step 3**: Buat database `leaderhub_db` di MySQL
```sql
CREATE DATABASE leaderhub_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Step 4**: Install Filament 5
```bash
composer require filament/filament:"^5.0"
php artisan filament:install --panels
# Saat ditanya panel ID, ketik: admin
```

**Step 5**: Require core & design-tokens (path repository dari root composer.json)
```bash
cd /run/media/salmon/data/Projects/leaderhub
composer update leaderhub/core leaderhub/design-tokens --with-all-dependencies 2>/dev/null || true

# Tambahkan require di apps/website/composer.json
cd apps/website
composer require leaderhub/core:"@dev" --no-update
```

Edit `apps/website/composer.json`, tambahkan repositories lokal:
```json
{
  "repositories": [
    { "type": "path", "url": "../../core" },
    { "type": "path", "url": "../../packages/design-tokens" }
  ],
  "require": {
    "php": "^8.2",
    "leaderhub/core": "@dev"
  }
}
```

```bash
composer update
```

**Step 6**: Register CoreServiceProvider di website
Edit `apps/website/config/app.php` (atau `bootstrap/providers.php` di Laravel 12):
```php
// bootstrap/providers.php
return [
    App\Providers\AppServiceProvider::class,
    Leaderhub\Core\CoreServiceProvider::class,
];
```

**Step 7**: Setup Tailwind 4 + design tokens
```bash
pnpm install
pnpm add -D tailwindcss @tailwindcss/vite
```

Edit `apps/website/vite.config.js`:
```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  plugins: [
    laravel({ input: ['resources/css/app.css', 'resources/js/app.js'], refresh: true }),
    tailwindcss(),
  ],
});
```

Buat `apps/website/resources/css/app.css`:
```css
@import "../../packages/design-tokens/dist/tokens.css";
@import "tailwindcss";

@theme {
  --font-sans: 'Inter', system-ui, sans-serif;
  --font-mono: ui-monospace, 'SF Mono', Menlo, monospace;
}
```

**Step 8**: Test serve
```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/website
php artisan key:generate
php artisan migrate --path=../../core/database/migrations
php artisan serve --port=8000
```

Buka `http://localhost:8000` → harus load tanpa error.

### Validasi P0-T04
- [ ] `php artisan serve --port=8000` → load di browser
- [ ] `php artisan tinker` → `Leaderhub\Core\Models\Test::all()` berfungsi (return collection)
- [ ] Tabel `tests` ada di database
- [ ] `php artisan filament:install` berhasil, `/admin/login` accessible

---

## Task P0-T05: Setup apps/hub (Laravel 12 + Filament 5)

### Tujuan
Fresh install Laravel 12 untuk LeaderP ERP, install Filament 5 + Shield.

### Step-by-Step

**Step 1**: Install Laravel 12
```bash
cd /run/media/salmon/data/Projects/leaderhub/apps
composer create-project laravel/laravel hub
cd hub
```

**Step 2**: Konfigurasi `.env`
```bash
APP_NAME="LeaderP"
APP_URL=http://localhost:8001
DB_DATABASE=leaderhub_db   # shared database
# ... sama seperti website
```

**Step 3**: Install Filament 5 + Shield
```bash
composer require filament/filament:"^5.0" bezhansalleh/filament-shield:"^5.0"
php artisan filament:install --panels
# Panel ID: hub
php artisan shield:install
```

**Step 4**: Require core
Edit `apps/hub/composer.json`:
```json
{
  "repositories": [
    { "type": "path", "url": "../../core" },
    { "type": "path", "url": "../../packages/design-tokens" }
  ],
  "require": {
    "php": "^8.2",
    "leaderhub/core": "@dev"
  }
}
```
```bash
composer update
```

**Step 5**: Register CoreServiceProvider
Edit `apps/hub/bootstrap/providers.php`:
```php
return [
    App\Providers\AppServiceProvider::class,
    Leaderhub\Core\CoreServiceProvider::class,
];
```

**Step 6**: Test serve
```bash
php artisan key:generate
php artisan serve --port=8001
```

Buka `http://localhost:8001/hub/login` → Filament login page tampil.

### Validasi P0-T05
- [ ] `php artisan serve --port=8001` → load
- [ ] `/hub/login` accessible (Filament panel)
- [ ] `php artisan tinker` → `Leaderhub\Core\Models\Test::all()` berfungsi

---

## Task P0-T06: Setup apps/gym (fork laravel-gymie + multi-tenant)

### Tujuan
Copy laravel-gymie sebagai base, install stancl/tenancy, require core.

### Step-by-Step

**Step 1**: Copy laravel-gymie ke apps/gym
```bash
cp -r /run/media/salmon/data/Projects/templates/laravel-gymie /run/media/salmon/data/Projects/leaderhub/apps/gym
cd /run/media/salmon/data/Projects/leaderhub/apps/gym
rm -rf .git .github
```

**Step 2**: Bersihkan & update dependencies
```bash
composer install
# Pastikan Laravel 12 + Filament 5 (gymie sudah pakai versi ini)
```

**Step 3**: Konfigurasi `.env`
```bash
APP_NAME="LeadGym"
APP_URL=http://localhost:8002
DB_DATABASE=leaderhub_db   # shared

# Tenancy config
TENANCY_DOMAIN=gym.leaderhub.test
TENANCY_IDENTIFICATION_MODE=subdomain
```

**Step 4**: Install stancl/tenancy 3.x
```bash
composer require stancl/tenancy:"^3.0"
php artisan vendor:publish --provider="Stancl\Tenancy\TenancyServiceProvider"
```

Edit `config/tenancy.php` (mode single-DB):
```php
'database' => [
    'central_connection' => env('DB_CONNECTION', 'mysql'),
    'tenant_connection' => env('DB_CONNECTION', 'mysql'), // same DB, pakai tenant_id
],
'identification' => [
    'mode' => 'subdomain',
],
```

**Step 5**: Require core
Edit `apps/gym/composer.json`:
```json
{
  "repositories": [
    { "type": "path", "url": "../../core" },
    { "type": "path", "url": "../../packages/design-tokens" }
  ],
  "require": {
    "php": "^8.2",
    "leaderhub/core": "@dev"
  }
}
```
```bash
composer update
```

**Step 6**: Register CoreServiceProvider
Edit `apps/gym/bootstrap/providers.php`:
```php
return [
    App\Providers\AppServiceProvider::class,
    Leaderhub\Core\CoreServiceProvider::class,
];
```

**Step 7**: Test serve
```bash
php artisan key:generate
php artisan serve --port=8002
```

Buka `http://localhost:8002` → load.

### Validasi P0-T06
- [ ] `php artisan serve --port=8002` → load
- [ ] `php artisan tinker` → `Leaderhub\Core\Models\Test::all()` berfungsi
- [ ] `composer show stancl/tenancy` menampilkan versi 3.x

---

## Task P0-T07: Configure pnpm workspace & root composer

### Tujuan
Pastikan `pnpm install` di root berhasil untuk semua app.

### Step-by-Step

**Step 1**: Pastikan tiap app punya `package.json` dengan name unik
```bash
# apps/website/package.json
{ "name": "@leaderhub/website", "private": true, ... }

# apps/hub/package.json
{ "name": "@leaderhub/hub", "private": true, ... }

# apps/gym/package.json
{ "name": "@leaderhub/gym", "private": true, ... }
```

**Step 2**: Install dependencies semua app dari root
```bash
cd /run/media/salmon/data/Projects/leaderhub
pnpm install
```

**Step 3**: Test build tokens
```bash
pnpm build:tokens
# Harus menghasilkan packages/design-tokens/dist/* terisi
```

### Validasi P0-T07
- [ ] `pnpm install` di root berhasil tanpa error
- [ ] `pnpm build:tokens` berhasil
- [ ] `pnpm -r list` menampilkan semua package workspace

---

## Task P0-T08: Test core model accessible dari ketiga app

### Tujuan
Memastikan shared model di core bisa dipanggil dari website, hub, gym.

### Step-by-Step

**Step 1**: Pastikan migration tests sudah jalan di tiap app
```bash
cd apps/website && php artisan migrate --path=../../core/database/migrations --force
cd ../hub && php artisan migrate --path=../../core/database/migrations --force
cd ../gym && php artisan migrate --path=../../core/database/migrations --force
```

**Step 2**: Test insert dari tiap app
```bash
# Website
cd apps/website
php artisan tinker
>>> Leaderhub\Core\Models\Test::create(['name' => 'from-website']);
>>> exit

# Hub
cd ../hub
php artisan tinker
>>> Leaderhub\Core\Models\Test::create(['name' => 'from-hub']);
>>> Leaderhub\Core\Models\Test::all();  // harus tampil 2 record
>>> exit

# Gym
cd ../gym
php artisan tinker
>>> Leaderhub\Core\Models\Test::create(['name' => 'from-gym']);
>>> Leaderhub\Core\Models\Test::all();  // harus tampil 3 record
>>> exit
```

### Validasi P0-T08
- [ ] Insert dari website → terlihat di hub & gym
- [ ] Insert dari hub → terlihat di website & gym
- [ ] Insert dari gym → terlihat di website & hub
- [ ] Shared database terkonfirmasi (3 record di tabel `tests`)

---

## Task P0-T09: Setup shared storage symlink

### Tujuan
Ketiga app share folder storage untuk upload file.

### Step-by-Step

**Step 1**: Buat shared storage folder
```bash
cd /run/media/salmon/data/Projects/leaderhub
mkdir -p storage/app/public
```

**Step 2**: Hapus storage/app/public default di tiap app & symlink ke shared
```bash
# Website
rm -rf apps/website/storage/app/public
ln -s ../../../storage/app/public apps/website/storage/app/public

# Hub
rm -rf apps/hub/storage/app/public
ln -s ../../../storage/app/public apps/hub/storage/app/public

# Gym
rm -rf apps/gym/storage/app/public
ln -s ../../../storage/app/public apps/gym/storage/app/public
```

**Step 3**: Test upload dari tiap app bisa diakses app lain
```bash
# Website upload test
cd apps/website
php artisan tinker
>>> \Storage::disk('public')->put('test-from-website.txt', 'hello');
>>> exit

# Hub baca
cd ../hub
php artisan tinker
>>> \Storage::disk('public')->exists('test-from-website.txt');  // true
>>> exit
```

### Validasi P0-T09
- [ ] `ls -la apps/website/storage/app/public` → symlink ke `../../../storage/app/public`
- [ ] File upload dari website bisa dibaca di hub & gym
- [ ] File upload dari hub bisa dibaca di website & gym

---

## Task P0-T10: Initial git commit

### Step-by-Step

```bash
cd /run/media/salmon/data/Projects/leaderhub
git add -A
git status   # review dulu, pastikan tidak ada .env atau secrets
git commit -m "Phase 0: Monorepo setup — core, design-tokens, 3 app skeleton

- core/ package (PSR-4 Leaderhub\Core\)
- packages/design-tokens/ (Style Dictionary build)
- apps/website (Laravel 12 + Filament 5)
- apps/hub (Laravel 12 + Filament 5 + Shield)
- apps/gym (fork laravel-gymie + stancl/tenancy)
- shared storage symlink
- pnpm workspace configured"
```

### Validasi P0-T10
- [ ] `git log` menampilkan 1 commit
- [ ] `git status` clean
- [ ] Tidak ada `.env` yang ter-commit (cek `git log --name-only`)

---

## Checklist Akhir Phase 0

Jalankan semua validasi ini sebelum lanjut ke Phase 1:

```bash
# 1. Website load
cd /run/media/salmon/data/Projects/leaderhub/apps/website
php artisan serve --port=8000 &
sleep 2
curl -s http://localhost:8000 | grep -q "Laravel" && echo "✓ Website OK" || echo "✗ Website FAIL"
kill %1

# 2. Hub load
cd ../hub
php artisan serve --port=8001 &
sleep 2
curl -s http://localhost:8001/hub/login | grep -q "Filament" && echo "✓ Hub OK" || echo "✗ Hub FAIL"
kill %1

# 3. Gym load
cd ../gym
php artisan serve --port=8002 &
sleep 2
curl -s http://localhost:8002 | grep -q "Laravel\|Gymie" && echo "✓ Gym OK" || echo "✗ Gym FAIL"
kill %1

# 4. Core model accessible
cd /run/media/salmon/data/Projects/leaderhub/apps/website
php artisan tinker --execute="echo Leaderhub\Core\Models\Test::count() . ' records';"

# 5. Design tokens
cd /run/media/salmon/data/Projects/leaderhub/packages/design-tokens
pnpm build && ls dist/

# 6. Git clean
cd /run/media/salmon/data/Projects/leaderhub
git status --porcelain
```

**Jika semua ✓ → lanjut Phase 1. Jika ada ✗ → fix dulu, jangan lanjut.**