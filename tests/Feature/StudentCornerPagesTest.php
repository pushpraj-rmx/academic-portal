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

test('students verification search finds student by enrollment id', function () {
    $student = Student::factory()->create([
        'enrollment_id' => 'ENR-TEST-001',
    ]);
    $student->load('user');

    $response = $this->get(route('students.verification.search', [
        'query' => 'ENR-TEST-001',
    ]));

    $response->assertSuccessful();
    $response->assertSee('Student Found', false);
    $response->assertSee('ENR-TEST-001', false);
});

test('students verification search finds student by roll number', function () {
    $student = Student::factory()->create([
        'roll_number' => 'ROLL-UNIQUE-999',
    ]);

    $response = $this->get(route('students.verification.search', [
        'query' => 'ROLL-UNIQUE-999',
    ]));

    $response->assertSuccessful();
    $response->assertSee('Student Found', false);
    $response->assertSee('ROLL-UNIQUE-999', false);
});

test('students verification search does not find student when query does not match', function () {
    Student::factory()->create([
        'enrollment_id' => 'ENR-TEST-002',
    ]);

    $response = $this->get(route('students.verification.search', [
        'query' => 'NONEXISTENT-QUERY-XYZ',
    ]));

    $response->assertSuccessful();
    $response->assertSee(settings('empty_student_not_found', 'No student found'), false);
    $response->assertDontSee('Student Found', false);
});

test('students verification shows father and mother names when present', function () {
    $student = Student::factory()->create([
        'roll_number' => 'ROLL-PARENT-001',
        'father_name' => 'Ramesh Sharma',
        'mother_name' => 'Sita Sharma',
    ]);

    $student->load('user', 'course');

    $response = $this->get(route('students.verification.search', [
        'query' => 'ROLL-PARENT-001',
    ]));

    $response->assertSuccessful();
    $response->assertSee('Father name', false);
    $response->assertSee('Ramesh Sharma', false);
    $response->assertSee('Mother name', false);
    $response->assertSee('Sita Sharma', false);
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
