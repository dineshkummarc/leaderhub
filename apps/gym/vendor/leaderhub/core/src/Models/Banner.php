<?php
namespace Leaderhub\Core\Models;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model {
    protected $guarded = [];
    protected $casts = [
        'is_active' => 'boolean'
    ];
}
