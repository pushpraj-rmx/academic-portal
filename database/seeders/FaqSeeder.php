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
                'answer' => 'Go to Examination > Download Exam Form and click download.',
                'category' => 'examination',
                'sort_order' => 1,
            ],
            [
                'question' => 'How can I check my result?',
                'answer' => 'Go to Students Corner > Results and search using roll number or enrollment ID.',
                'category' => 'examination',
                'sort_order' => 2,
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
