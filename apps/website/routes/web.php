<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ThemeController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/api/theme', [ThemeController::class, 'toggleTheme']);
Route::post('/api/lang', [ThemeController::class, 'toggleLang']);

use App\Http\Controllers\PageController;
Route::get('/{page:slug}', [PageController::class, 'show'])->where('page', '[a-z0-9-]+');
