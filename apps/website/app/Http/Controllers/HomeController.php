<?php

namespace App\Http\Controllers;

use Leaderhub\Core\Models\Banner;
use Leaderhub\Core\Models\Category;
use Leaderhub\Core\Models\Product;
use Leaderhub\Core\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Product::with(['images', 'category'])->featured()->orderBy('created_at', 'desc')->take(8)->get();
        $heroSettings = Setting::get('hero_product_ids', 'hero', '[]');
        $heroIds = json_decode($heroSettings, true) ?: [];

        // Use admin-selected hero products (up to 3) or fallback to featured
        $heroProducts = collect();
        if (! empty($heroIds)) {
            $heroProducts = Product::with(['images', 'category'])
                ->whereIn('id', $heroIds)
                ->get();
        }
        if ($heroProducts->isEmpty()) {
            $heroProducts = $featured->take(3);
        }

        $data = [
            'banners' => Banner::where('is_active', true)->orderBy('sort_order')->get(),
            'hero_products' => $heroProducts,
            'featured_products' => $featured,
            'categories' => Category::where('is_active', true)->whereNull('parent_id')->orderBy('sort_order')->get(),
        ];

        return view('home', $data);
    }
}
