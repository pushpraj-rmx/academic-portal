<?php

use App\Models\DownloadableForm;
use App\Models\Faq;
use App\Models\GradingRule;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(SiteSettingSeeder::class);
});

test('contact page can be viewed and submitted', function () {
    $this->get(route('contact.index'))
        ->assertSuccessful();

    $response = $this->post(route('contact.store'), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '9999999999',
        'subject' => 'Admission',
        'message' => 'Please share admission process details.',
    ]);

    $response->assertRedirect(route('contact.index'));
    $this->assertDatabaseHas('contact_submissions', [
        'email' => 'john@example.com',
        'subject' => 'Admission',
    ]);
});

test('examination faqs page shows examination faqs', function () {
    Faq::factory()->create([
        'question' => 'Exam date?',
        'category' => 'examination',
        'is_published' => true,
    ]);
    Faq::factory()->create([
        'question' => 'Hidden FAQ',
        'category' => 'examination',
        'is_published' => false,
    ]);

    $response = $this->get(route('examination.faqs'));

    $response->assertSuccessful();
    $response->assertSee('Exam date?', false);
    $response->assertDontSee('Hidden FAQ', false);
});

test('grading system page shows grading rules', function () {
    GradingRule::factory()->create([
        'grade' => 'A+',
        'min_percentage' => 90,
        'max_percentage' => 100,
        'gpa' => 10,
    ]);

    $response = $this->get(route('examination.grading-system'));

    $response->assertSuccessful();
    $response->assertSee('A+', false);
    $response->assertSee('90.00', false);
});

test('exam forms page shows forms and downloadable file', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->create('exam-form.pdf', 20, 'application/pdf');
    $storedPath = $file->store('downloadable-forms', 'public');

    $form = DownloadableForm::factory()->create([
        'name' => 'Examination Form 2025',
        'category' => 'exam',
        'file_path' => $storedPath,
        'is_published' => true,
    ]);

    $response = $this->get(route('examination.forms'));
    $response->assertSuccessful();
    $response->assertSee('Examination Form 2025', false);

    $downloadResponse = $this->get(route('downloads.form', $form));
    $downloadResponse->assertSuccessful();
});
