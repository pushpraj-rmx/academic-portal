<?php

use App\Models\SiteSetting;
use Database\Seeders\PageSeeder;
use Database\Seeders\SiteSettingSeeder;

beforeEach(function () {
    $this->seed(PageSeeder::class);
    $this->seed(SiteSettingSeeder::class);
});

test('home page renders with default site settings in footer', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee(config('app.name'), false);
    $response->assertSee('All rights reserved', false);
});

test('results index shows section title and intro from site settings', function () {
    $response = $this->get(route('results.index'));

    $response->assertSuccessful();
    $response->assertSee(SiteSetting::get('section_check_results', 'Check Results'), false);
    $response->assertSee(SiteSetting::get('results_intro', 'Enter your roll number'), false);
});

test('placements recruiters page shows section title from site settings', function () {
    $response = $this->get(route('placements.recruiters'));

    $response->assertSuccessful();
    $response->assertSee(SiteSetting::get('section_recruiters', 'Our Recruiters'), false);
});

test('updated site setting value is reflected on next request', function () {
    SiteSetting::updateOrCreate(
        ['key' => 'section_recruiters'],
        ['value' => 'Our Hiring Partners']
    );

    $response = $this->get(route('placements.recruiters'));

    $response->assertSuccessful();
    $response->assertSee('Our Hiring Partners', false);
});
