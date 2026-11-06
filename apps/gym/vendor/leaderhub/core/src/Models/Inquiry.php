<?php
namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inquiry extends Model {
    protected $guarded = [];

    protected function casts(): array {
        return ['uuid' => 'string'];
    }

    public function items(): HasMany {
        return $this->hasMany(InquiryItem::class);
    }
}
