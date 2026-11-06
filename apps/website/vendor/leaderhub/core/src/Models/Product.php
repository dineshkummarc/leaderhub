<?php
namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model {
    protected $guarded = [];

    protected static function booted(): void {
        static::addGlobalScope('published', function (Builder $builder) {
            $builder->where('status', 'published');
        });
    }

    public function scopeFeatured($query) {
        return $query->where('is_featured', true);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function brand() {
        return $this->belongsTo(Brand::class);
    }

    public function images() {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function specs() {
        return $this->hasMany(ProductSpec::class)->orderBy('sort_order');
    }

    public function stockLevels() {
        return $this->hasMany(StockLevel::class);
    }

    public function getFeaturedImageAttribute() {
        return $this->images->first()?->path;
    }

    public function getTotalStockAttribute() {
        return $this->stockLevels->sum('quantity');
    }

    public function resolveRouteBinding($value, $field = null) {
        return $this->withoutGlobalScope('published')->where($field ?? $this->getRouteKeyName(), $value)->firstOrFail();
    }
}
