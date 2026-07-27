<?php
namespace Leaderhub\Core\Models;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model {
    protected $guarded = [];
    public function stockLevels() { return $this->hasMany(StockLevel::class); }
}
