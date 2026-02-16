<?php

use App\Models\Course;
use App\Models\ExamForm;
use App\Models\ExamSession;
use App\Models\ExamSessionSubject;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectMark;
use App\Services\ExamResultService;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('result not included when subject mark is missing for one subject', function () {
    $session = ExamSession::factory()->create(['status' => 'completed']);
    $course = Course::factory()->create();
    $student = Student::factory()->create(['course_id' => $course->id]);
    $sub1 = Subject::factory()->forCourse($course)->create(['max_marks' => 100, 'passing_marks' => 40]);
    $sub2 = Subject::factory()->forCourse($course)->create(['max_marks' => 100, 'passing_marks' => 40]);
    ExamSessionSubject::create(['exam_session_id' => $session->id, 'subject_id' => $sub1->id]);
    ExamSessionSubject::create(['exam_session_id' => $session->id, 'subject_id' => $sub2->id]);
    ExamForm::create([
        'exam_session_id' => $session->id,
        'student_id' => $student->id,
        'status' => 'approved',
        'approved_at' => now(),
    ]);
    SubjectMark::create([
        'exam_session_id' => $session->id,
        'student_id' => $student->id,
        'subject_id' => $sub1->id,
        'marks_obtained' => 80,
        'is_absent' => false,
    ]);

    $service = app(ExamResultService::class);
    $results = $service->computeResultsForSession($session);

    expect($results)->toHaveCount(0);
});

test('result included when all subject marks present and grade computed', function () {
    $session = ExamSession::factory()->create(['status' => 'completed']);
    $course = Course::factory()->create();
    $student = Student::factory()->create(['course_id' => $course->id]);
    $sub1 = Subject::factory()->forCourse($course)->create(['max_marks' => 100, 'passing_marks' => 40]);
    $sub2 = Subject::factory()->forCourse($course)->create(['max_marks' => 100, 'passing_marks' => 40]);
    ExamSessionSubject::create(['exam_session_id' => $session->id, 'subject_id' => $sub1->id]);
    ExamSessionSubject::create(['exam_session_id' => $session->id, 'subject_id' => $sub2->id]);
    ExamForm::create([
        'exam_session_id' => $session->id,
        'student_id' => $student->id,
        'status' => 'approved',
        'approved_at' => now(),
    ]);
    SubjectMark::create([
        'exam_session_id' => $session->id,
        'student_id' => $student->id,
        'subject_id' => $sub1->id,
        'marks_obtained' => 85,
        'is_absent' => false,
    ]);
    SubjectMark::create([
        'exam_session_id' => $session->id,
        'student_id' => $student->id,
        'subject_id' => $sub2->id,
        'marks_obtained' => 75,
        'is_absent' => false,
    ]);

    $service = app(ExamResultService::class);
    $results = $service->computeResultsForSession($session);

    expect($results)->toHaveCount(1);
    expect($results[0]['student_id'])->toBe($student->id);
    expect($results[0]['total_marks'])->toBe(200.0);
    expect($results[0]['marks_obtained'])->toBe(160.0);
    expect($results[0]['percentage'])->toBe(80.0);
    expect($results[0]['grade'])->toBe('A');
    expect($results[0]['is_passed'])->toBeTrue();
});

test('absent in any subject sets is_passed false', function () {
    $session = ExamSession::factory()->create(['status' => 'completed']);
    $course = Course::factory()->create();
    $student = Student::factory()->create(['course_id' => $course->id]);
    $sub1 = Subject::factory()->forCourse($course)->create(['max_marks' => 100, 'passing_marks' => 40]);
    $sub2 = Subject::factory()->forCourse($course)->create(['max_marks' => 100, 'passing_marks' => 40]);
    ExamSessionSubject::create(['exam_session_id' => $session->id, 'subject_id' => $sub1->id]);
    ExamSessionSubject::create(['exam_session_id' => $session->id, 'subject_id' => $sub2->id]);
    ExamForm::create([
        'exam_session_id' => $session->id,
        'student_id' => $student->id,
        'status' => 'approved',
        'approved_at' => now(),
    ]);
    SubjectMark::create([
        'exam_session_id' => $session->id,
        'student_id' => $student->id,
        'subject_id' => $sub1->id,
        'marks_obtained' => null,
        'is_absent' => true,
    ]);
    SubjectMark::create([
        'exam_session_id' => $session->id,
        'student_id' => $student->id,
        'subject_id' => $sub2->id,
        'marks_obtained' => 90,
        'is_absent' => false,
    ]);

    $service = app(ExamResultService::class);
    $results = $service->computeResultsForSession($session);

    expect($results)->toHaveCount(1);
    expect($results[0]['is_passed'])->toBeFalse();
});
