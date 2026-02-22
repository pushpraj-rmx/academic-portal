<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Predefined keys and default values. Used by seeder and Filament admin.
     *
     * @return array<string, string>
     */
    public static function defaults(): array
    {
        return [
            'footer_text' => '© :year :name. All rights reserved.',
            'ticker_label' => 'Notices:',
            'empty_notices' => 'No announcements at the moment.',
            'empty_course_categories' => 'No course categories available.',
            'empty_recruiters' => 'No recruiters to display.',
            'empty_placement_by_year' => 'No placement data by year.',
            'empty_placement_by_course' => 'No placement data by course.',
            'empty_courses_in_category' => 'No courses in this category at the moment.',
            'empty_student_not_found' => 'No student found with that roll number or enrollment ID.',
            'empty_no_results' => 'No published results found for this student.',
            'back_to_notices' => 'Back to Notices',
            'back_to_courses' => 'Back to Courses',
            'section_recruiters' => 'Our Recruiters',
            'section_placement_statistics' => 'Placement Statistics',
            'section_highest_package' => 'Highest Package (CTC / Stipend)',
            'section_placements_by_year' => 'Placements by Academic Year',
            'section_placements_by_course' => 'Placements by Course',
            'section_notices_announcements' => 'Notices & Announcements',
            'section_courses' => 'Courses',
            'section_check_results' => 'Check Results',
            'section_exam_results' => 'Exam Results',
            'results_intro' => 'Enter your roll number or enrollment ID to view your exam results.',
            'placement_statistics_link' => 'View placement statistics',
            'currency_symbol' => '₹',
            'lpa_label' => 'LPA',
        ];
    }

    /**
     * Get a setting value by key, with request-level cache.
     * Returns default when key is missing or value is null.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        $cacheKey = 'site_settings';

        $settings = Cache::remember($cacheKey, now()->addSeconds(30), function () {
            return self::query()->pluck('value', 'key')->toArray();
        });

        $value = $settings[$key] ?? $default;

        return $value === null || $value === '' ? $default : (string) $value;
    }

    /**
     * Clear the site settings cache (e.g. after update in admin).
     */
    public static function clearCache(): void
    {
        Cache::forget('site_settings');
    }

    protected static function booted(): void
    {
        static::saved(fn () => self::clearCache());
        static::deleted(fn () => self::clearCache());
    }
}
