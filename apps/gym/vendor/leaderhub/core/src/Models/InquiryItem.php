<?php
namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InquiryItem extends Model {
    protected $guarded = [];

    public function inquiry(): BelongsTo {
        return $this->belongsTo(Inquiry::class);
    }

    public function product(): BelongsTo {
        return $this->belongsTo(Product::class);
    }
}
