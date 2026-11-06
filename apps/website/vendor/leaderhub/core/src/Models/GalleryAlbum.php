<?php
namespace Leaderhub\Core\Models;
use Illuminate\Database\Eloquent\Model;

class GalleryAlbum extends Model {
    protected $guarded = [];
    public function items() { return $this->hasMany(GalleryItem::class)->orderBy('sort_order'); }
}
