<?php
namespace Leaderhub\Core\Models;
use Illuminate\Database\Eloquent\Model;

class ProductSpec extends Model {
    protected $guarded = [];
    public function product() { return $this->belongsTo(Product::class); }
}
