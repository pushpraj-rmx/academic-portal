<?php

use App\Models\Certification;
use App\Models\HeroSlide;
use App\Models\NavItem;
use App\Models\Recruiter;
use App\Models\StatCounter;
use App\Models\Testimonial;
use Database\Seeders\NavItemSeeder;
use Database\Seeders\PageSeeder;
use Database\Seeders\SiteSettingSeeder;

beforeEach(function () {
    $this->seed(PageSeeder::class);
    $this->seed(SiteSettingSeeder::class);
});

test('home page renders new managed sections', function () {
    HeroSlide::factory()->create(['title' => 'Welcome to Our Institute', 'is_published' => true]);
    StatCounter::factory()->create(['label' => 'Teachers', 'value' => 60, 'is_published' => true]);
    Certification::factory()->create(['name' => 'IEEE', 'is_published' => true]);
    Testimonial::factory()->create(['name' => 'Jane Doe', 'body' => 'Great experience.', 'is_published' => true]);
    Recruiter::factory()->create(['name' => 'Top Recruiter', 'is_active' => true]);

    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('Welcome to Our Institute', false);
    $response->assertSee('Teachers', false);
    $response->assertSee('IEEE', false);
    $response->assertSee('WHAT PEOPLE SAYS', false);
    $response->assertSee('Top Recruiter', false);
});

test('home page hides unpublished managed section records', function () {
    HeroSlide::factory()->unpublished()->create(['title' => 'Hidden Slide']);
    StatCounter::factory()->unpublished()->create(['label' => 'Hidden Stat']);
    Certification::factory()->unpublished()->create(['name' => 'Hidden Certification']);

    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertDontSee('Hidden Slide', false);
    $response->assertDontSee('Hidden Stat', false);
    $response->assertDontSee('Hidden Certification', false);
});

test('header navigation is driven by nav items from database', function () {
    $this->seed(NavItemSeeder::class);

    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('Home', false);
    $response->assertSee('About Us', false);
    $response->assertSee('Contact', false);
    $response->assertSee('Examination', false);
    $response->assertSee('Students Corner', false);
});

test('hidden nav items are not shown in header', function () {
    $this->seed(NavItemSeeder::class);
    $custom = NavItem::create([
        'label' => 'Unique Nav Link XyZ',
        'route_name' => 'home',
        'sort_order' => 99,
        'is_visible' => true,
    ]);
    $response = $this->get(route('home'));
    $response->assertSuccessful();
    $response->assertSee('Unique Nav Link XyZ', false);

    $custom->update(['is_visible' => false]);
    $response = $this->get(route('home'));
    $response->assertSuccessful();
    $response->assertDontSee('Unique Nav Link XyZ', false);
});
