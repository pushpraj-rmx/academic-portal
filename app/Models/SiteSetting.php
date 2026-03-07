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
            'logo_path' => '',
            'footer_text' => '© :year :name. All rights reserved.',
            'footer_address' => '',
            'footer_phone' => '',
            'footer_emails' => '[]',
            'topbar_welcome' => '',
            'topbar_phone' => '',
            'topbar_email' => '',
            'topbar_location' => '',
            'payu_url' => '',
            'facebook_url' => '',
            'linkedin_url' => '',
            'google_plus_url' => '',
            'twitter_url' => '',
            'pinterest_url' => '',
            'vimeo_url' => '',
            'ticker_label' => 'Notices:',
            'testimonial_section_title' => 'WHAT PEOPLE SAYS',
            'testimonial_section_subtitle' => 'Fusce sem dolor, interdum in efficitur at, faucibus nec lorem. Sed nec molestie justo.',
            'empty_testimonials' => 'No testimonials at the moment.',
            'empty_notices' => 'No announcements at the moment.',
            'empty_course_categories' => 'No course categories available.',
            'empty_recruiters' => 'No recruiters to display.',
            'empty_placement_by_year' => 'No placement data by year.',
            'empty_placement_by_course' => 'No placement data by course.',
            'empty_courses_in_category' => 'No courses in this category at the moment.',
            'empty_student_not_found' => 'No student found with that roll number or enrollment ID.',
            'empty_no_results' => 'No published results found for this student.',
            'empty_examination_faqs' => 'No FAQs available right now.',
            'empty_grading_rules' => 'No grading rules available right now.',
            'empty_exam_forms' => 'No exam forms available right now.',
            'empty_application_forms' => 'No application forms available right now.',
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
            'section_hero' => 'Welcome to Our Institute',
            'section_welcome' => 'Welcome',
            'section_stats' => 'Achievements',
            'section_certifications' => 'Certifications',
            'section_students_corner' => 'Students Corner',
            'section_examination' => 'Examination',
            'results_intro' => 'Enter your roll number or enrollment ID to view your exam results.',
            'placement_statistics_link' => 'View placement statistics',
            'welcome_section_title' => 'Welcome to Our Institute',
            'welcome_section_body' => '',
            'stats_section_title' => 'Achievements',
            'certifications_section_title' => 'Certifications',
            'employers_section_title' => 'Our Recruiters',
            'two_column_image' => '',
            'two_column_image_alt' => '',
            'two_column_title' => '',
            'two_column_body' => '',
            'two_column_image_first' => '1',
            'contact_page_title' => 'Contact',
            'contact_page_subtitle' => 'Get in touch with us for any query.',
            'examination_center_address' => '',
            'examination_center_description' => '',
            'examination_center_map_embed' => '',
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
