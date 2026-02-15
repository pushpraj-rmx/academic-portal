<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Database\Seeder;

class CourseCategorySeeder extends Seeder
{
    /**
     * Seed sample course categories (and minimal courses) for demos and local development.
     */
    public function run(): void
    {
        $undergraduate = CourseCategory::firstOrCreate(
            ['slug' => 'undergraduate'],
            [
                'name' => 'Undergraduate',
                'description' => 'Bachelor and integrated degree programs.',
                'is_active' => true,
                'sort_order' => 0,
            ]
        );

        Course::firstOrCreate(
            ['slug' => 'bsc-computer-science'],
            [
                'course_category_id' => $undergraduate->id,
                'name' => 'B.Sc. Computer Science',
                'duration' => '3 years',
                'intake' => 'Annual',
                'eligibility' => '10+2 with PCM/PCB',
                'description' => '<p>A comprehensive program covering core computer science and applications.</p>',
                'is_active' => true,
                'sort_order' => 0,
            ]
        );

        $postgraduate = CourseCategory::firstOrCreate(
            ['slug' => 'postgraduate'],
            [
                'name' => 'Postgraduate',
                'description' => 'Master and doctoral programs.',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        Course::firstOrCreate(
            ['slug' => 'mca'],
            [
                'course_category_id' => $postgraduate->id,
                'name' => 'M.C.A.',
                'duration' => '2 years',
                'intake' => 'Annual',
                'eligibility' => 'Graduate with Mathematics',
                'description' => '<p>Master of Computer Applications for graduates seeking IT careers.</p>',
                'is_active' => true,
                'sort_order' => 0,
            ]
        );
    }
}
