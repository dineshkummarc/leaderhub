<?php

use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ThemeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');

Route::get('/sitemap.xml', [SitemapController::class, 'index']);

Route::post('/api/theme', [ThemeController::class, 'toggleTheme']);
Route::post('/api/lang', [ThemeController::class, 'toggleLang']);

use App\Http\Controllers\BlogController;
use App\Http\Controllers\InquiryController;

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/inquiry', [InquiryController::class, 'index'])->name('inquiry.index');
Route::post('/inquiry', [InquiryController::class, 'store'])->name('inquiry.store');

use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;

Route::get('/{page:slug}', [PageController::class, 'show'])->where('page', '[a-z0-9-]+');
