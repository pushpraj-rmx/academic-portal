<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::published()
            ->latest('published_at')
            ->paginate(10);

        return view('public.notices.index', ['announcements' => $announcements]);
    }

    public function show(Announcement $announcement): Response
    {
        if (! $announcement->is_published) {
            abort(404);
        }

        return response()->view('public.notices.show', ['announcement' => $announcement]);
    }
}
