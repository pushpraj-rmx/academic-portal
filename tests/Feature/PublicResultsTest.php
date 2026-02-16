<?php

use App\Models\ExamForm;
use App\Models\ExamSession;
use App\Models\Student;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('results index returns 200', function () {
    $response = $this->get(route('results.index'));

    $response->assertSuccessful();
});

test('results search with no match shows empty student', function () {
    $response = $this->get(route('results.search', ['query' => 'NONEXISTENT999']));

    $response->assertSuccessful();
    $response->assertViewHas('student', null);
    $response->assertViewHas('resultsBySession', []);
});

test('results search with valid roll number shows only published sessions', function () {
    $student = Student::factory()->create();
    $sessionPublished = ExamSession::factory()->published()->create();
    $sessionDraft = ExamSession::factory()->create(['status' => 'draft']);
    ExamForm::create([
        'exam_session_id' => $sessionPublished->id,
        'student_id' => $student->id,
        'status' => 'approved',
        'approved_at' => now(),
    ]);
    ExamForm::create([
        'exam_session_id' => $sessionDraft->id,
        'student_id' => $student->id,
        'status' => 'approved',
        'approved_at' => now(),
    ]);

    $response = $this->get(route('results.search', ['query' => $student->roll_number]));

    $response->assertSuccessful();
    $response->assertViewHas('student', $student);
    $resultsBySession = $response->viewData('resultsBySession');
    expect($resultsBySession)->toHaveCount(0);
});
