<?php

use App\Models\Course;
use App\Models\ExamForm;
use App\Models\ExamSession;
use App\Models\ExamSessionSubject;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectMark;
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

test('results page shows father and mother names when present', function () {
    $course = Course::factory()->create();

    $student = Student::factory()->create([
        'course_id' => $course->id,
        'roll_number' => 'ROLL-PARENTS-999',
        'father_name' => 'Rahul Dad',
        'mother_name' => 'Priya Mom',
    ]);

    $session = ExamSession::factory()->published()->create();
    $subject = Subject::factory()->forCourse($course)->create();

    ExamSessionSubject::create([
        'exam_session_id' => $session->id,
        'subject_id' => $subject->id,
    ]);

    ExamForm::create([
        'exam_session_id' => $session->id,
        'student_id' => $student->id,
        'status' => 'approved',
        'approved_at' => now(),
    ]);

    SubjectMark::create([
        'exam_session_id' => $session->id,
        'student_id' => $student->id,
        'subject_id' => $subject->id,
        'marks_obtained' => 80,
        'is_absent' => false,
    ]);

    $response = $this->get(route('results.search', ['query' => $student->roll_number]));

    $response->assertSuccessful();
    $response->assertSee('Father name', false);
    $response->assertSee('Rahul Dad', false);
    $response->assertSee('Mother name', false);
    $response->assertSee('Priya Mom', false);
});

test('results search allows selecting exam session', function () {
    $course = Course::factory()->create();
    $student = Student::factory()->create([
        'course_id' => $course->id,
        'roll_number' => 'ROLL-SESSION-SELECT-1',
        'father_name' => 'Father Select',
        'mother_name' => 'Mother Select',
    ]);

    $sub1 = Subject::factory()->forCourse($course)->create(['max_marks' => 100, 'passing_marks' => 35]);
    $sub2 = Subject::factory()->forCourse($course)->create(['max_marks' => 100, 'passing_marks' => 35]);

    $session1 = ExamSession::factory()->published()->create(['start_date' => now()->subDays(20)]);
    $session2 = ExamSession::factory()->published()->create(['start_date' => now()->subDays(10)]);

    foreach ([$sub1->id, $sub2->id] as $sid) {
        ExamSessionSubject::create(['exam_session_id' => $session1->id, 'subject_id' => $sid]);
        ExamSessionSubject::create(['exam_session_id' => $session2->id, 'subject_id' => $sid]);
    }

    ExamForm::create([
        'exam_session_id' => $session1->id,
        'student_id' => $student->id,
        'status' => 'approved',
        'approved_at' => now(),
    ]);
    ExamForm::create([
        'exam_session_id' => $session2->id,
        'student_id' => $student->id,
        'status' => 'approved',
        'approved_at' => now(),
    ]);

    // Session 1 marks
    SubjectMark::create([
        'exam_session_id' => $session1->id,
        'student_id' => $student->id,
        'subject_id' => $sub1->id,
        'marks_obtained' => 80,
        'is_absent' => false,
    ]);
    SubjectMark::create([
        'exam_session_id' => $session1->id,
        'student_id' => $student->id,
        'subject_id' => $sub2->id,
        'marks_obtained' => 75,
        'is_absent' => false,
    ]);

    // Session 2 marks
    SubjectMark::create([
        'exam_session_id' => $session2->id,
        'student_id' => $student->id,
        'subject_id' => $sub1->id,
        'marks_obtained' => 60,
        'is_absent' => false,
    ]);
    SubjectMark::create([
        'exam_session_id' => $session2->id,
        'student_id' => $student->id,
        'subject_id' => $sub2->id,
        'marks_obtained' => 50,
        'is_absent' => false,
    ]);

    $response = $this->get(route('results.search', [
        'query' => $student->roll_number,
        'exam_session_id' => $session2->id,
    ]));

    $response->assertSuccessful();
    $resultsBySession = $response->viewData('resultsBySession');

    expect($resultsBySession)->toHaveCount(1);
    expect($resultsBySession[0]['session']->id)->toBe($session2->id);
});
