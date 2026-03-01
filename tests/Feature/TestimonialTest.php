<?php

use App\Models\SiteSetting;
use App\Models\Testimonial;
use Database\Seeders\PageSeeder;
use Database\Seeders\SiteSettingSeeder;

beforeEach(function () {
    $this->seed(PageSeeder::class);
    $this->seed(SiteSettingSeeder::class);
});

test('home page shows testimonials section when published testimonials exist', function () {
    Testimonial::factory()->create([
        'name' => 'Aliana Dsuza',
        'body' => 'Etiam non elit nec augue tempor gravida et sed velit.',
        'is_published' => true,
    ]);

    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('WHAT PEOPLE SAYS', false);
    $response->assertSee('Aliana Dsuza', false);
    $response->assertSee('Etiam non elit nec augue tempor gravida et sed velit.', false);
});

test('home page does not show testimonials section when no published testimonials', function () {
    Testimonial::factory()->unpublished()->create(['name' => 'Hidden Person', 'body' => 'Hidden text.']);

    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertDontSee('Hidden Person', false);
    $response->assertDontSee('Hidden text.', false);
});

test('testimonials section title and subtitle come from site settings', function () {
    SiteSetting::updateOrCreate(['key' => 'testimonial_section_title'], ['value' => 'WHAT OUR STUDENTS SAY']);
    SiteSetting::updateOrCreate(['key' => 'testimonial_section_subtitle'], ['value' => 'Read their stories.']);
    Testimonial::factory()->create(['name' => 'Jane Doe', 'body' => 'Great college.']);

    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('WHAT OUR STUDENTS SAY', false);
    $response->assertSee('Read their stories.', false);
    $response->assertSee('Jane Doe', false);
});
