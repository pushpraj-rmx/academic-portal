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

test('footer contact block shows address, phone and emails when set', function () {
    SiteSetting::updateOrCreate(['key' => 'footer_address'], ['value' => 'D-2 Vishnu Datt Marg, Block-D, Janakpuri']);
    SiteSetting::updateOrCreate(['key' => 'footer_phone'], ['value' => '+91 1234567890']);
    SiteSetting::updateOrCreate(['key' => 'footer_emails'], ['value' => json_encode(['info@example.com', 'admission@example.com'])]);

    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('Address', false);
    $response->assertSee('Phone Number', false);
    $response->assertSee('Email Address', false);
    $response->assertSee('D-2 Vishnu Datt Marg', false);
    $response->assertSee('+91 1234567890', false);
    $response->assertSee('info@example.com', false);
    $response->assertSee('admission@example.com', false);
});

test('footer social links are shown when URLs are set', function () {
    SiteSetting::updateOrCreate(['key' => 'facebook_url'], ['value' => 'https://facebook.com/example']);
    SiteSetting::updateOrCreate(['key' => 'twitter_url'], ['value' => 'https://twitter.com/example']);

    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('https://facebook.com/example', false);
    $response->assertSee('https://twitter.com/example', false);
});

test('footer copyright text is shown from site settings', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('All rights reserved', false);
});
