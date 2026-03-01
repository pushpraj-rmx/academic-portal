<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use Illuminate\Database\Seeder;

class HeroSlideSeeder extends Seeder
{
    public function run(): void
    {
        $slides = [
            [
                'title' => 'Welcome to Our Institute',
                'subtitle' => 'Career enhancing courses with lots of opportunities for a better future.',
                'button_text' => 'View Courses',
                'button_link' => route('academic.index'),
                'sort_order' => 1,
            ],
            [
                'title' => 'Are You Ready to Apply?',
                'subtitle' => 'Admissions open for session 2025-2026.',
                'button_text' => 'Download Application Form',
                'button_link' => route('students.application-forms'),
                'sort_order' => 2,
            ],
        ];

        foreach ($slides as $slide) {
            HeroSlide::query()->updateOrCreate(
                ['title' => $slide['title']],
                array_merge($slide, ['is_published' => true, 'image' => null])
            );
        }
    }
}
