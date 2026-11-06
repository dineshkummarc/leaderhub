<?php

namespace Leaderhub\Core\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ImageService
{
    protected $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    public function uploadAndOptimize(UploadedFile $file, $path = 'uploads', $quality = 80)
    {
        $image = $this->manager->read($file->getRealPath());
        
        $filename = uniqid() . '.webp';
        $fullPath = $path . '/' . $filename;
        
        $encoded = (string) $image->toWebp($quality);
        
        Storage::disk('public')->put($fullPath, $encoded);
        
        return $fullPath;
    }

    public function generateThumbnail($sourcePath, $width = 400)
    {
        if (!Storage::disk('public')->exists($sourcePath)) {
            return null;
        }

        $imageContents = Storage::disk('public')->get($sourcePath);
        $image = $this->manager->read($imageContents);
        
        $image->scale(width: $width);
        
        $pathInfo = pathinfo($sourcePath);
        $thumbPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumb.webp';
        
        $encoded = (string) $image->toWebp(80);
        Storage::disk('public')->put($thumbPath, $encoded);
        
        return $thumbPath;
    }

    public function srcset($path)
    {
        $url = Storage::url($path);
        $pathInfo = pathinfo($path);
        $thumbPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumb.webp';
        
        if (Storage::disk('public')->exists($thumbPath)) {
            $thumbUrl = Storage::url($thumbPath);
            return "{$thumbUrl} 400w, {$url} 800w";
        }
        
        return "{$url} 800w";
    }
}
