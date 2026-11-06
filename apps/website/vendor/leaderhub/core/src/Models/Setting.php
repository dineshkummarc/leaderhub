<?php
namespace Leaderhub\Core\Models;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model {
    protected $guarded = [];
    protected $casts = ['is_translatable' => 'boolean'];
    
    public static function get($key, $group = 'general', $default = null) {
        $setting = self::where('key', $key)->where('group', $group)->first();
        return $setting ? $setting->value : $default;
    }
    
    public static function set($key, $value, $group = 'general') {
        return self::updateOrCreate(['key' => $key, 'group' => $group], ['value' => $value]);
    }
}
