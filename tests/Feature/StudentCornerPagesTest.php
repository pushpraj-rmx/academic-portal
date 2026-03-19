<?php

use App\Models\DownloadableForm;
use App\Models\Student;
use Database\Seeders\PageSeeder;
use Database\Seeders\SiteSettingSeeder;

beforeEach(function () {
    $this->seed(PageSeeder::class);
    $this->seed(SiteSettingSeeder::class);
});

test('students verification page is accessible', function () {
    $response = $this->get(route('students.verification'));

    $response->assertSuccessful();
    $response->assertSee('Students Verification', false);
});

test('students verification search finds student by enrollment id and dob', function () {
    $student = Student::factory()->create([
        'enrollment_id' => 'ENR-TEST-001',
        'date_of_birth' => '2000-05-15',
    ]);
    $student->load('user');

    $response = $this->get(route('students.verification.search', [
        'query' => 'ENR-TEST-001',
        'dob' => '2000-05-15',
    ]));

    $response->assertSuccessful();
    $response->assertSee('Student Found', false);
    $response->assertSee('ENR-TEST-001', false);
});

test('students verification search does not find student when dob does not match', function () {
    $student = Student::factory()->create([
        'enrollment_id' => 'ENR-TEST-002',
        'date_of_birth' => '2000-05-15',
    ]);

    $response = $this->get(route('students.verification.search', [
        'query' => 'ENR-TEST-002',
        'dob' => '1999-01-01',
    ]));

    $response->assertSuccessful();
    $response->assertSee(settings('empty_student_not_found', 'No student found'), false);
    $response->assertDontSee('Student Found', false);
});

test('students application forms page shows admission forms only', function () {
    DownloadableForm::factory()->create([
        'name' => 'Admission Form',
        'category' => 'admission',
        'is_published' => true,
    ]);
    DownloadableForm::factory()->create([
        'name' => 'EXAM-FORM-UNIQUE-HIDDEN',
        'category' => 'exam',
        'is_published' => true,
    ]);

    $response = $this->get(route('students.application-forms'));

    $response->assertSuccessful();
    $response->assertSee('Admission Form', false);
    $response->assertDontSee('EXAM-FORM-UNIQUE-HIDDEN', false);
});

test('students pay fee page renders cms content', function () {
    $response = $this->get(route('students.pay-fee'));

    $response->assertSuccessful();
    $response->assertSee('Pay Fee', false);
});
