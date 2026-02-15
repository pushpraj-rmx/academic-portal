<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $announcements = [
            [
                'title' => 'Academic Calendar 2024-25 Released',
                'slug' => 'academic-calendar-2024-25-released',
                'body' => 'The academic calendar for the session 2024-25 has been published. Please refer to the attachment for detailed dates.',
                'attachment' => null,
                'is_published' => true,
                'published_at' => now()->subDays(10),
            ],
            [
                'title' => 'Holiday List - Semester Break',
                'slug' => 'holiday-list-semester-break',
                'body' => 'The list of holidays during the semester break is now available.',
                'attachment' => null,
                'is_published' => true,
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Examination Schedule Notice',
                'slug' => 'examination-schedule-notice',
                'body' => 'Students are advised to check the examination schedule for end-semester exams.',
                'attachment' => null,
                'is_published' => true,
                'published_at' => now()->subDay(),
            ],
            [
                'title' => 'Draft: Upcoming Workshop',
                'slug' => 'draft-upcoming-workshop',
                'body' => 'This is a draft announcement. Details to be finalised.',
                'attachment' => null,
                'is_published' => false,
                'published_at' => null,
            ],
            [
                'title' => 'Draft: Fee Structure Update',
                'slug' => 'draft-fee-structure-update',
                'body' => 'Draft notice for fee structure revision.',
                'attachment' => null,
                'is_published' => false,
                'published_at' => null,
            ],
        ];

        foreach ($announcements as $data) {
            Announcement::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
