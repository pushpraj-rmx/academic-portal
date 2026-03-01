<?php

use App\Models\Page;
use Database\Seeders\PageSeeder;

beforeEach(function () {
    $this->seed(PageSeeder::class);
});

test('home page renders when published home page exists', function () {
    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertViewIs('public.home');
    $response->assertSee('Home', false);
});

test('published page is accessible at page slug URL', function () {
    $page = Page::where('slug', 'about')->first();
    $response = $this->get(route('pages.show', $page));

    $response->assertSuccessful();
    $response->assertSee($page->title, false);
});

test('unpublished page returns 404', function () {
    $page = Page::factory()->create([
        'slug' => 'draft-page',
        'title' => 'Draft Page',
        'is_published' => false,
    ]);

    $response = $this->get(route('pages.show', $page));

    $response->assertNotFound();
});

test('non-existent page slug returns 404', function () {
    $response = $this->get('/non-existent-slug');

    $response->assertNotFound();
});
