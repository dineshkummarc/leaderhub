<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Leaderhub\Core\Models\Brand;
use Leaderhub\Core\Models\Category;
use Leaderhub\Core\Models\Product;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['images', 'category']);

        // Search
        if ($request->filled('q')) {
            if (DB::connection()->getDriverName() !== 'sqlite') {
                $query->whereFullText(['name', 'sku'], $request->q);
            } else {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->q.'%')
                        ->orWhere('sku', 'like', '%'.$request->q.'%');
                });
            }
        }

        // Filter Category
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter Brand
        if ($request->filled('brand')) {
            $query->whereHas('brand', function ($q) use ($request) {
                $q->where('slug', $request->brand);
            });
        }

        // Filter Price
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        return view('products.index', compact('products', 'categories', 'brands'));
    }

    public function show($slug)
    {
        $product = Product::withoutGlobalScope('published')
            ->with(['images', 'specs', 'category', 'brand'])
            ->where('slug', $slug)
            ->firstOrFail();

        if ($product->status !== 'published' && ! auth()->check()) {
            abort(404);
        }

        $related_products = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'related_products'));
    }
}
