<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Home',
                'slug' => 'home',
                'body' => '<p>Welcome to our institute. We are committed to excellence in education and research.</p><p>Explore our programs, facilities, and the opportunities we offer.</p>',
                'meta_description' => 'Welcome to our autonomous institute.',
                'is_published' => true,
                'sort_order' => 0,
            ],
            [
                'title' => 'About Us',
                'slug' => 'about',
                'body' => '<p>Our institute has a rich history of academic excellence.</p><p>We provide quality education and foster innovation.</p>',
                'meta_description' => 'Learn about our institute history and mission.',
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'title' => "Director's Message",
                'slug' => 'director-message',
                'body' => '<p>It is my pleasure to welcome you to our institute.</p><p>We strive to create an environment where every student can achieve their potential.</p>',
                'meta_description' => 'A message from our Director.',
                'is_published' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Vision & Mission',
                'slug' => 'vision-mission',
                'body' => '<p><strong>Vision:</strong> To be a leading institution in education and research.</p><p><strong>Mission:</strong> To nurture talent and contribute to society through excellence in teaching and innovation.</p>',
                'meta_description' => 'Our vision and mission.',
                'is_published' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($pages as $data) {
            Page::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
