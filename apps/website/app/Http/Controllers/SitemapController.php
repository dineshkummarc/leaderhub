<?php

namespace App\Http\Controllers;

use Leaderhub\Core\Models\Category;
use Leaderhub\Core\Models\Product;

class SitemapController extends Controller
{
    public function index()
    {
        $products = Product::get();
        $categories = Category::where('is_active', true)->get();

        return response()->view('sitemap', compact('products', 'categories'))
            ->header('Content-Type', 'text/xml');
    }
}
