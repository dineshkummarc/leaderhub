<?php

namespace App\Http\Controllers;

use Leaderhub\Core\Models\GalleryAlbum;

class GalleryController extends Controller
{
    public function index()
    {
        $albums = GalleryAlbum::with('items')->orderBy('sort_order')->get();

        return view('gallery.index', compact('albums'));
    }
}
