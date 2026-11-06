<?php
namespace Leaderhub\Core\Models;
use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model {
    protected $guarded = [];
    public function album() { return $this->belongsTo(GalleryAlbum::class, 'gallery_album_id'); }
}
