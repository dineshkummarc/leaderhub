<?php
namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BlogPost extends Model {
    protected $guarded = [];

    protected function casts(): array {
        return ['published_at' => 'datetime'];
    }

    protected static function booted(): void {
        static::addGlobalScope('published', function (Builder $q) {
            $q->where('status', 'published')->where('published_at', '<=', now());
        });
    }

    public function category(): BelongsTo {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author(): BelongsTo {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags(): BelongsToMany {
        return $this->belongsToMany(BlogTag::class, 'blog_post_tags');
    }
}
