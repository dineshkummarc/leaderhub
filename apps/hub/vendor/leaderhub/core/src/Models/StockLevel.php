<?php
namespace Leaderhub\Core\Models;
use Illuminate\Database\Eloquent\Model;

class StockLevel extends Model {
    protected $guarded = [];
    public function product() { return $this->belongsTo(Product::class); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
}
