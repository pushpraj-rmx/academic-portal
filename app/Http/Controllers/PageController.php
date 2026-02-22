<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Response;

class PageController extends Controller
{
    public function home(): Response
    {
        $page = Page::published()->where('slug', 'home')->first();

        if (! $page) {
            abort(404);
        }

        $homeCardSlugs = ['about', 'notices', 'director-message'];
        $homeCardPages = Page::published()
            ->whereIn('slug', $homeCardSlugs)
            ->get()
            ->sortBy(fn (Page $p): int => array_search($p->slug, $homeCardSlugs, true))
            ->values();

        return response()->view('public.home', [
            'page' => $page,
            'homeCardPages' => $homeCardPages,
        ]);
    }

    public function show(Page $page): Response
    {
        if (! $page->is_published) {
            abort(404);
        }

        return response()->view('public.page', ['page' => $page]);
    }
}
