<?php

namespace Database\Seeders;

use App\Models\DownloadableForm;
use Illuminate\Database\Seeder;

class DownloadableFormSeeder extends Seeder
{
    public function run(): void
    {
        $forms = [
            [
                'name' => 'Examination Form 2025',
                'description' => 'Examination application form for current session.',
                'category' => 'exam',
                'sort_order' => 1,
            ],
            [
                'name' => 'Admission Application Form',
                'description' => 'Application form for admissions.',
                'category' => 'admission',
                'sort_order' => 1,
            ],
        ];

        foreach ($forms as $form) {
            DownloadableForm::query()->updateOrCreate(
                ['name' => $form['name']],
                array_merge($form, ['file_path' => 'downloadable-forms/sample.pdf', 'is_published' => true])
            );
        }
    }
}
