<?php

namespace App\Http\Controllers;

use Leaderhub\Core\Models\Page;

class PageController extends Controller
{
    public function show(Page $page)
    {
        $title = $page->title;
        $content = $page->content;

        if (app()->getLocale() === 'en') {
            $title = $page->title_en ?: $title;
            $content = $page->content_en ?: $content;
        }

        return view('page.show', compact('page', 'title', 'content'));
    }
}
