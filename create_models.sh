#!/bin/bash
set -e

CORE_MODELS="core/src/Models"
CORE_MIG="core/database/migrations"
mkdir -p "$CORE_MODELS" "$CORE_MIG"

# 1. User
cat << 'PHP' > "$CORE_MODELS/User.php"
<?php
namespace Leaderhub\Core\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use Notifiable;
    protected $guarded = [];
    protected $hidden = ['password', 'remember_token'];
    protected function casts(): array {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed'];
    }
}
PHP

cat << 'PHP' > "$CORE_MIG/2026_01_01_000010_create_users_table.php"
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role', 50)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('users'); }
};
PHP

# 2. Setting
cat << 'PHP' > "$CORE_MODELS/Setting.php"
<?php
namespace Leaderhub\Core\Models;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model {
    protected $guarded = [];
    protected $casts = ['is_translatable' => 'boolean'];
    
    public static function get($key, $group = 'general', $default = null) {
        $setting = self::where('key', $key)->where('group', $group)->first();
        return $setting ? $setting->value : $default;
    }
    
    public static function set($key, $value, $group = 'general') {
        return self::updateOrCreate(['key' => $key, 'group' => $group], ['value' => $value]);
    }
}
PHP

cat << 'PHP' > "$CORE_MIG/2026_01_01_000020_create_settings_table.php"
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50)->default('general');
            $table->string('key', 100);
            $table->text('value')->nullable();
            $table->string('type', 20)->default('text');
            $table->boolean('is_translatable')->default(false);
            $table->timestamps();
            $table->unique(['group', 'key']);
        });
    }
    public function down(): void { Schema::dropIfExists('settings'); }
};
PHP

# 3. Page
cat << 'PHP' > "$CORE_MODELS/Page.php"
<?php
namespace Leaderhub\Core\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Page extends Model {
    protected $guarded = [];
    
    protected static function booted(): void {
        static::addGlobalScope('published', function (Builder $builder) {
            $builder->where('status', 'published');
        });
    }
    
    public function resolveRouteBinding($value, $field = null) {
        return $this->withoutGlobalScope('published')->where($field ?? $this->getRouteKeyName(), $value)->firstOrFail();
    }
}
PHP

cat << 'PHP' > "$CORE_MIG/2026_01_01_000030_create_pages_table.php"
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('title_en')->nullable();
            $table->longText('content_en')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_image')->nullable();
            $table->string('status', 20)->default('draft');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('pages'); }
};
PHP

# 4. Translation
cat << 'PHP' > "$CORE_MODELS/Translation.php"
<?php
namespace Leaderhub\Core\Models;
use Illuminate\Database\Eloquent\Model;
class Translation extends Model { protected $guarded = []; }
PHP
cat << 'PHP' > "$CORE_MIG/2026_01_01_000040_create_translations_table.php"
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 10);
            $table->string('group');
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();
            $table->unique(['locale', 'group', 'key']);
        });
    }
    public function down(): void { Schema::dropIfExists('translations'); }
};
PHP

# 5. Category
cat << 'PHP' > "$CORE_MODELS/Category.php"
<?php
namespace Leaderhub\Core\Models;
use Illuminate\Database\Eloquent\Model;
class Category extends Model { protected $guarded = []; }
PHP
cat << 'PHP' > "$CORE_MIG/2026_01_01_000050_create_categories_table.php"
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->text('description_en')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('categories'); }
};
PHP

# 6. Brand
cat << 'PHP' > "$CORE_MODELS/Brand.php"
<?php
namespace Leaderhub\Core\Models;
use Illuminate\Database\Eloquent\Model;
class Brand extends Model { protected $guarded = []; }
PHP
cat << 'PHP' > "$CORE_MIG/2026_01_01_000060_create_brands_table.php"
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('brands'); }
};
PHP

# Remove default migrations in apps so they use core
rm -f apps/website/database/migrations/0001_01_01_000000_create_users_table.php
rm -f apps/hub/database/migrations/0001_01_01_000000_create_users_table.php
rm -f apps/gym/database/migrations/0001_01_01_000000_create_users_table.php
rm -f apps/website/app/Models/User.php
rm -f apps/hub/app/Models/User.php
rm -f apps/gym/app/Models/User.php

# Clear cache and run migrations
cd apps/website
php artisan migrate:fresh
