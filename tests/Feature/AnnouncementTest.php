<?php

use App\Models\Announcement;
use Database\Seeders\AnnouncementSeeder;

beforeEach(function () {
    $this->seed(AnnouncementSeeder::class);
});

test('notices index shows only published announcements', function () {
    $response = $this->get(route('notices.index'));

    $response->assertSuccessful();
    $response->assertViewIs('public.notices.index');
    $response->assertSee('Academic Calendar 2024-25 Released', false);
    $response->assertDontSee('Draft: Upcoming Workshop', false);
});

test('published announcement is accessible at notices slug URL', function () {
    $announcement = Announcement::where('slug', 'academic-calendar-2024-25-released')->first();

    $response = $this->get(route('notices.show', $announcement));

    $response->assertSuccessful();
    $response->assertSee($announcement->title, false);
});

test('unpublished announcement returns 404', function () {
    $announcement = Announcement::where('slug', 'draft-upcoming-workshop')->first();

    $response = $this->get(route('notices.show', $announcement));

    $response->assertNotFound();
});

test('notices index paginates results', function () {
    Announcement::query()->delete();
    Announcement::factory()->published()->count(15)->create();

    $response = $this->get(route('notices.index'));

    $response->assertSuccessful();
    $response->assertViewHas('announcements');
    expect($response->viewData('announcements')->count())->toBeLessThanOrEqual(10);
});
