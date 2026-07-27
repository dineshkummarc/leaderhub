<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function toggleTheme(Request $request)
    {
        $theme = session('theme', 'light') === 'light' ? 'dark' : 'light';
        session(['theme' => $theme]);

        return response()->json(['theme' => $theme]);
    }

    public function toggleLang(Request $request)
    {
        $locale = session('locale', config('app.locale')) === 'id' ? 'en' : 'id';
        session(['locale' => $locale]);

        return response()->json(['locale' => $locale]);
    }
}
