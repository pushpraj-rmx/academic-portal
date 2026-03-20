<?php

namespace Database\Seeders;

use App\Models\NavItem;
use Illuminate\Database\Seeder;

class NavItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['label' => 'Home', 'route_name' => 'home', 'sort_order' => 0],
            ['label' => 'About Us', 'route_name' => 'pages.show', 'route_params' => ['page' => 'about'], 'sort_order' => 1],
            ['label' => "Director's Message", 'route_name' => 'pages.show', 'route_params' => ['page' => 'director-message'], 'sort_order' => 2],
            ['label' => 'Courses', 'dynamic_source' => 'course_categories', 'sort_order' => 3],
            ['label' => 'Results', 'route_name' => 'results.index', 'sort_order' => 4],
            ['label' => 'Examination', 'sort_order' => 5],
            ['label' => 'Students Corner', 'sort_order' => 6],
            ['label' => 'Contact', 'route_name' => 'contact.index', 'sort_order' => 7],
            ['label' => 'Login', 'route_name' => 'login', 'show_when' => 'guest', 'sort_order' => 8],
            ['label' => 'Admin', 'url' => '/admin', 'show_when' => 'auth_admin', 'sort_order' => 9],
        ];

        foreach ($items as $data) {
            NavItem::firstOrCreate(
                ['label' => $data['label'], 'parent_id' => null],
                [
                    'route_name' => $data['route_name'] ?? null,
                    'route_params' => $data['route_params'] ?? null,
                    'url' => $data['url'] ?? null,
                    'sort_order' => $data['sort_order'],
                    'is_visible' => true,
                    'show_when' => $data['show_when'] ?? null,
                    'dynamic_source' => $data['dynamic_source'] ?? null,
                ]
            );
        }

        foreach ($items as $data) {
            NavItem::query()
                ->whereNull('parent_id')
                ->where('label', $data['label'])
                ->update(['sort_order' => $data['sort_order']]);
        }

        $examination = NavItem::whereNull('parent_id')->where('label', 'Examination')->first();
        if ($examination) {
            $children = [
                ['label' => 'Download Exam Form', 'route_name' => 'examination.forms', 'sort_order' => 0],
                ['label' => 'Examination Center', 'route_name' => 'examination.center', 'sort_order' => 1],
                ['label' => 'Examination FAQs', 'route_name' => 'examination.faqs', 'sort_order' => 2],
                ['label' => 'Grading System', 'route_name' => 'examination.grading-system', 'sort_order' => 3],
            ];
            foreach ($children as $data) {
                NavItem::firstOrCreate(
                    ['label' => $data['label'], 'parent_id' => $examination->id],
                    [
                        'route_name' => $data['route_name'],
                        'sort_order' => $data['sort_order'],
                        'is_visible' => true,
                    ]
                );
            }
        }

        // Results is a top-level nav item; remove legacy duplicate under Students Corner if present
        NavItem::query()
            ->where('label', 'Results')
            ->whereNotNull('parent_id')
            ->delete();

        $studentsCorner = NavItem::whereNull('parent_id')->where('label', 'Students Corner')->first();
        if ($studentsCorner) {
            $children = [
                ['label' => 'Download Application Form', 'route_name' => 'students.application-forms', 'sort_order' => 0],
                ['label' => 'Students Verification', 'route_name' => 'students.verification', 'sort_order' => 1],
                ['label' => 'Placement', 'route_name' => 'placements.recruiters', 'sort_order' => 2],
                ['label' => 'Pay Fee', 'route_name' => 'students.pay-fee', 'sort_order' => 3],
            ];
            foreach ($children as $data) {
                NavItem::firstOrCreate(
                    ['label' => $data['label'], 'parent_id' => $studentsCorner->id],
                    [
                        'route_name' => $data['route_name'],
                        'sort_order' => $data['sort_order'],
                        'is_visible' => true,
                    ]
                );
            }
        }
    }
}
