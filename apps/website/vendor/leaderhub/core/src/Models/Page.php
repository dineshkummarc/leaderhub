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
