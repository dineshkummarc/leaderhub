<?php
namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BlogTag extends Model {
    protected $guarded = [];

    public function posts(): BelongsToMany {
        return $this->belongsToMany(BlogPost::class, 'blog_post_tags');
    }
}
