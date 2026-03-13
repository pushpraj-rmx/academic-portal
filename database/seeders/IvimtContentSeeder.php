<?php

namespace Database\Seeders;

use App\Models\Certification;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\HeroSlide;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Models\StatCounter;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class IvimtContentSeeder extends Seeder
{
    /**
     * Seed IVIMT-specific content (branding, pages, hero, stats, certifications, courses, testimonials).
     * Run after default seeders. Uses updateOrCreate so it overlays existing data.
     */
    public function run(): void
    {
        $this->seedSiteSettings();
        $this->seedPages();
        $this->seedHeroSlides();
        $this->seedStatCounters();
        $this->seedCertifications();
        $this->seedCourseCategoriesAndCourses();
        $this->seedTestimonials();
    }

    private function seedSiteSettings(): void
    {
        $settings = [
            'topbar_welcome' => 'Welcome to Indravati Vinay Institute of Management & Technology',
            'footer_address' => 'Rohini Sector 3, North West New Delhi – 110041',
            'footer_phone' => '',
            'footer_text' => '© '.date('Y').' Indravati Vinay Institute of Management & Technology (IVIMT). All rights reserved.',
            'topbar_phone' => '',
            'topbar_email' => '',
            'topbar_location' => 'Rohini Sector 3, North West New Delhi – 110041',
            'welcome_section_title' => 'Welcome to Our Institute',
            'welcome_section_body' => 'INDRAVATI VINAY INSTITUTE OF MANAGEMENT & TECHNOLOGY (IVIMT) — We, established in 2001, are committed to excellence in fast-track distance learning, management education, and regular programmes in India. We deliver measurable outcomes and invite you to discover what makes IVIMT a source of managerial and technical talent. IVIMT is an autonomous body dedicated to the development of personality and skills through quality education and research. The Institute stands for humanism, tolerance, reason, and the search for truth. Why Choose Us — Quality programmes, experienced faculty, and a supportive learning environment.',
            'stats_section_title' => 'Achievements',
            'certifications_section_title' => 'Certifications',
            'employers_section_title' => 'IVIMT Employers',
            'testimonial_section_title' => 'What People Say',
            'testimonial_section_subtitle' => 'Hear from our students and alumni about their experience at IVIMT.',
            'section_courses' => 'Our Main Courses',
            'contact_page_title' => 'Contact',
            'contact_page_subtitle' => 'Get in touch with us for any query.',
            'examination_center_address' => 'Rohini Sector 3, North West New Delhi – 110041',
            'examination_center_description' => 'Examinations are conducted at our designated centre. Address and timings will be communicated with the admit card.',
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }

    private function seedPages(): void
    {
        $pages = [
            [
                'slug' => 'home',
                'title' => 'Home',
                'body' => '<p>Welcome to Indravati Vinay Institute of Management & Technology (IVIMT). We are committed to excellence in education, established in 2001. Explore our programmes, facilities, and opportunities.</p>',
                'meta_description' => 'Indravati Vinay Institute of Management & Technology (IVIMT) — Quality management and technology education since 2001.',
                'is_published' => true,
                'sort_order' => 0,
            ],
            [
                'slug' => 'about',
                'title' => 'About Us',
                'body' => $this->aboutUsBody(),
                'meta_description' => 'Learn about IVIMT — vision, mission, faculty, and infrastructure.',
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'slug' => 'director-message',
                'title' => "Director's Message",
                'body' => $this->directorMessageBody(),
                'meta_description' => 'A message from the Director of IVIMT.',
                'is_published' => true,
                'sort_order' => 2,
            ],
            [
                'slug' => 'vision-mission',
                'title' => 'Vision & Mission',
                'body' => $this->visionMissionBody(),
                'meta_description' => 'Vision and mission of IVIMT.',
                'is_published' => true,
                'sort_order' => 3,
            ],
            [
                'slug' => 'pay-fee',
                'title' => 'Pay Fee',
                'body' => '<p>For fee payment details and options, please contact the accounts office or visit us at Rohini Sector 3, North West New Delhi – 110041.</p><p>You can also reach us by phone or email (to be updated).</p>',
                'meta_description' => 'Fee payment information for IVIMT.',
                'is_published' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($pages as $data) {
            Page::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }

    private function aboutUsBody(): string
    {
        return '<p>Indravati Vinay Institute of Management & Technology (IVIMT) was established in 2001 in Rohini, New Delhi. We offer diploma, undergraduate, and postgraduate programmes in Management, Computer Applications, and Engineering, with a focus on employability and industry relevance.</p>'
            .'<h2 class="text-xl font-semibold mt-6">Vision</h2><p>To be a leading institute in management and technology education, recognised for quality, innovation, and the success of our students.</p>'
            .'<h2 class="text-xl font-semibold mt-6">Mission</h2><p>To provide accessible, industry-aligned education; to develop competent and ethical professionals; and to contribute to society through teaching, research, and outreach.</p>'
            .'<h2 class="text-xl font-semibold mt-6">Academic Excellence</h2><p>Our programmes are designed to meet current industry needs. We emphasise both theory and practice through projects, case studies, and industry interaction.</p>'
            .'<h2 class="text-xl font-semibold mt-6">Faculty</h2><p>Our faculty bring academic and industry experience to the classroom. We support their professional development to maintain high teaching standards.</p>'
            .'<h2 class="text-xl font-semibold mt-6">Infrastructure</h2><p>We provide a conducive learning environment with classrooms, library, computer labs, and support for digital learning. Our campus is located at Rohini Sector 3, North West New Delhi – 110041.</p>';
    }

    private function directorMessageBody(): string
    {
        return '<p>Dear Students and Visitors,</p>'
            .'<p>It is my pleasure to welcome you to Indravati Vinay Institute of Management & Technology (IVIMT).</p>'
            .'<p>Education is the foundation of progress. At IVIMT we believe in combining academic rigour with practical skills so that every graduate is ready for the workplace. We focus on discipline, integrity, and continuous learning.</p>'
            .'<p>We encourage you to make the most of the programmes, faculty, and facilities. Our goal is to support your growth and help you achieve your career aspirations. I wish you success and invite you to be part of the IVIMT community.</p>'
            .'<p>With best wishes,</p>'
            .'<p><strong>Director</strong><br>Indravati Vinay Institute of Management & Technology (IVIMT)</p>';
    }

    private function visionMissionBody(): string
    {
        return '<h2 class="text-xl font-semibold">Vision</h2>'
            .'<p>To be a leading institution in management and technology education, known for quality, innovation, and the success of our students and alumni.</p>'
            .'<h2 class="text-xl font-semibold mt-6">Mission</h2>'
            .'<ul class="list-disc pl-6 mt-2 space-y-1"><li>To offer relevant, industry-aligned programmes in management, computer applications, and engineering.</li>'
            .'<li>To develop competent, ethical professionals through quality teaching and practical exposure.</li>'
            .'<li>To foster a culture of learning, research, and continuous improvement.</li>'
            .'<li>To contribute to society through skilled graduates and institutional outreach.</li></ul>';
    }

    private function seedHeroSlides(): void
    {
        $slides = [
            [
                'title' => 'Welcome to Our Institute',
                'subtitle' => 'Career enhancing courses with lots of opportunities for a better future.',
                'button_text' => 'Apply Now',
                'button_link' => route('contact.index'),
                'sort_order' => 1,
            ],
            [
                'title' => 'Are You Ready to Apply?',
                'subtitle' => 'Admission open — Session 2025–2026.',
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

    private function seedStatCounters(): void
    {
        $counters = [
            ['label' => 'Teachers', 'value' => 60, 'sort_order' => 1],
            ['label' => 'Courses', 'value' => 40, 'sort_order' => 2],
            ['label' => 'Students', 'value' => 900, 'sort_order' => 3],
            ['label' => 'Satisfied Client', 'value' => 3675, 'sort_order' => 4],
        ];

        foreach ($counters as $counter) {
            StatCounter::query()->updateOrCreate(
                ['label' => $counter['label']],
                array_merge($counter, ['suffix' => '', 'icon' => null, 'is_published' => true])
            );
        }
    }

    private function seedCertifications(): void
    {
        $certifications = [
            ['name' => 'CDG & DAC', 'sort_order' => 1],
            ['name' => 'JAS-ANZ & IAF', 'sort_order' => 2],
            ['name' => 'IEEE', 'sort_order' => 3],
        ];

        foreach ($certifications as $certification) {
            Certification::query()->updateOrCreate(
                ['name' => $certification['name']],
                array_merge($certification, ['image' => null, 'is_published' => true])
            );
        }
    }

    private function seedCourseCategoriesAndCourses(): void
    {
        $categories = [
            [
                'slug' => 'computer-courses',
                'name' => 'Computer Courses',
                'description' => 'Diploma, Graduate and Post Graduate programmes in Computer courses.',
                'sort_order' => 0,
            ],
            [
                'slug' => 'management-courses',
                'name' => 'Management Courses',
                'description' => 'Diploma, Graduate and Post Graduate programmes in Management courses.',
                'sort_order' => 1,
            ],
            [
                'slug' => 'engineering-courses',
                'name' => 'Engineering Courses',
                'description' => 'Diploma, Graduate and Post Graduate programmes in Engineering courses.',
                'sort_order' => 2,
            ],
        ];

        $courseTitles = [
            'computer-courses' => ['B.Sc. Computer Science', 'bsc-computer-science'],
            'management-courses' => ['MBA / BBA', 'mba-bba'],
            'engineering-courses' => ['B.Tech', 'btech'],
        ];

        foreach ($categories as $cat) {
            $category = CourseCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                array_merge($cat, ['is_active' => true])
            );

            [$courseName, $courseSlug] = $courseTitles[$cat['slug']];
            Course::firstOrCreate(
                ['slug' => $courseSlug],
                [
                    'course_category_id' => $category->id,
                    'name' => $courseName,
                    'duration' => 'As per programme',
                    'intake' => null,
                    'eligibility' => 'As per programme',
                    'description' => '<p>'.$cat['description'].'</p><p>For detailed eligibility, duration, and fee structure, please contact the institute or refer to the course brochure.</p>',
                    'is_active' => true,
                    'sort_order' => $cat['sort_order'],
                ]
            );
        }
    }

    private function seedTestimonials(): void
    {
        $testimonials = [
            ['name' => 'Priya Sharma', 'body' => 'The faculty and course structure at IVIMT helped me build a strong foundation. The placement support was timely and effective. I landed a role in a reputed firm right after completing my course.', 'sort_order' => 1],
            ['name' => 'Rahul Verma', 'body' => 'I chose IVIMT for its reputation and flexible programmes. The institute gave me the right balance of theory and practical exposure. The industry-relevant curriculum made the transition to my job smooth.', 'sort_order' => 2],
            ['name' => 'Anita Desai', 'body' => 'Quality education and a supportive environment. I am grateful for the skills and confidence I gained here. The faculty went the extra mile to clarify doubts and guide us through projects.', 'sort_order' => 3],
            ['name' => 'Vikram Singh', 'body' => 'The management courses are well-designed and the faculty is approachable. A good choice for working professionals who want to upskill without compromising on their jobs. Highly recommend IVIMT.', 'sort_order' => 4],
            ['name' => 'Sneha Reddy', 'body' => 'IVIMT opened doors for me. The certification and placement assistance were exactly what I needed to advance my career. The alumni network and industry tie-ups are a real advantage.', 'sort_order' => 5],
            ['name' => 'Arjun Mehta', 'body' => 'From day one, the focus was on practical learning. The computer labs, workshops, and industry visits added immense value. I feel well-prepared for the tech industry today.', 'sort_order' => 6],
            ['name' => 'Kavita Nair', 'body' => 'As a working professional, I needed a programme that fit my schedule. IVIMT delivered that without compromising on quality. The weekend batches and online support made it possible for me to complete my diploma.', 'sort_order' => 7],
            ['name' => 'Rohit Gupta', 'body' => 'The examination process was fair and transparent. The grading system was clear, and the faculty was always available to help us prepare. My experience at IVIMT has been rewarding in every way.', 'sort_order' => 8],
        ];

        foreach ($testimonials as $t) {
            Testimonial::query()->updateOrCreate(
                ['sort_order' => $t['sort_order']],
                array_merge($t, ['avatar' => null, 'is_published' => true])
            );
        }
    }
}
