<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'How can I download the exam form?',
                'answer' => 'Go to Examination > Download Exam Form on the website. Select the form you need and click the Download button. You will get a PDF that you can print, fill, and submit as per the institute guidelines.',
                'category' => 'examination',
                'sort_order' => 1,
            ],
            [
                'question' => 'How can I check my result?',
                'answer' => 'Go to Students Corner > Results (or Examination > Check Results). Enter your roll number or enrollment ID and click Search. Your result will be displayed if it has been published for your exam session.',
                'category' => 'examination',
                'sort_order' => 2,
            ],
            [
                'question' => 'Where is the examination center?',
                'answer' => 'The examination center address and map are available under Examination > Examination Center. Check the page for the full address and directions. Contact the institute if you need any clarification.',
                'category' => 'examination',
                'sort_order' => 3,
            ],
            [
                'question' => 'How is the grading system calculated?',
                'answer' => 'The grading system (grade points, percentage bands, and GPA) is published under Examination > Grading System. It explains how your marks are converted to grades and how the overall result is computed.',
                'category' => 'examination',
                'sort_order' => 4,
            ],
            [
                'question' => 'When are exam dates announced?',
                'answer' => 'Exam dates and the schedule are announced by the institute and the university. Keep checking the Notices & Announcements section and the Examination area on the website. You may also receive updates via email or notice board.',
                'category' => 'examination',
                'sort_order' => 5,
            ],
            [
                'question' => 'What if I am absent for an exam?',
                'answer' => 'If you are absent for an exam, you may be marked absent and your result may reflect the same. For re-examination or special exam provisions, you need to contact the examination cell or your course coordinator with valid reasons and supporting documents.',
                'category' => 'examination',
                'sort_order' => 6,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::query()->updateOrCreate(
                ['question' => $faq['question']],
                array_merge($faq, ['is_published' => true])
            );
        }
    }
}
