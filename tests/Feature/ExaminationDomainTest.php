<?php

use App\Models\Course;
use App\Models\ExamForm;
use App\Models\ExamSession;
use App\Models\ExamSessionSubject;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectMark;
use App\Models\User;
use App\Policies\ExamFormPolicy;
use App\Policies\SubjectMarkPolicy;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('exam session has valid status', function () {
    $session = ExamSession::factory()->create(['status' => 'draft']);
    expect($session->status)->toBe('draft');

    $session->update(['status' => 'registration_open']);
    expect($session->fresh()->status)->toBe('registration_open');
});

test('exam form can only be created when session is registration_open', function () {
    $session = ExamSession::factory()->create(['status' => 'draft']);
    $course = Course::factory()->create();
    $student = Student::factory()->create(['course_id' => $course->id]);
    Subject::factory()->forCourse($course)->create();
    ExamSessionSubject::create([
        'exam_session_id' => $session->id,
        'subject_id' => $course->subjects()->first()->id,
    ]);

    $session->update(['status' => 'registration_open']);
    $form = ExamForm::create([
        'exam_session_id' => $session->id,
        'student_id' => $student->id,
        'status' => 'applied',
    ]);
    expect($form)->not->toBeNull();
});

test('exam form uniqueness per session and student', function () {
    $session = ExamSession::factory()->create(['status' => 'registration_open']);
    $student = Student::factory()->create();
    $course = $student->course;
    $subject = Subject::factory()->forCourse($course)->create();
    ExamSessionSubject::create(['exam_session_id' => $session->id, 'subject_id' => $subject->id]);

    ExamForm::create([
        'exam_session_id' => $session->id,
        'student_id' => $student->id,
        'status' => 'applied',
    ]);

    expect(fn () => ExamForm::create([
        'exam_session_id' => $session->id,
        'student_id' => $student->id,
        'status' => 'applied',
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

test('exam form approve sets status approved_at approved_by', function () {
    $session = ExamSession::factory()->create(['status' => 'registration_open']);
    $student = Student::factory()->create();
    $subject = Subject::factory()->forCourse($student->course)->create();
    ExamSessionSubject::create(['exam_session_id' => $session->id, 'subject_id' => $subject->id]);
    $form = ExamForm::create([
        'exam_session_id' => $session->id,
        'student_id' => $student->id,
        'status' => 'applied',
    ]);
    $user = User::factory()->create();

    $form->update([
        'status' => 'approved',
        'approved_at' => now(),
        'approved_by' => $user->id,
    ]);

    $form->refresh();
    expect($form->status)->toBe('approved');
    expect($form->approved_at)->not->toBeNull();
    expect($form->approved_by)->toBe($user->id);
});

test('exam form policy denies update when session is published', function () {
    $session = ExamSession::factory()->create(['status' => 'published']);
    $form = ExamForm::factory()->create(['exam_session_id' => $session->id]);
    $user = User::factory()->create();
    $user->givePermissionTo('exam-form.update');

    $policy = new ExamFormPolicy;
    expect($policy->update($user, $form))->toBeFalse();
});

test('subject mark uniqueness per session student subject', function () {
    $session = ExamSession::factory()->create();
    $course = Course::factory()->create();
    $student = Student::factory()->create(['course_id' => $course->id]);
    $subject = Subject::factory()->forCourse($course)->create();
    ExamSessionSubject::create(['exam_session_id' => $session->id, 'subject_id' => $subject->id]);

    SubjectMark::create([
        'exam_session_id' => $session->id,
        'student_id' => $student->id,
        'subject_id' => $subject->id,
        'marks_obtained' => 50,
        'is_absent' => false,
    ]);

    expect(fn () => SubjectMark::create([
        'exam_session_id' => $session->id,
        'student_id' => $student->id,
        'subject_id' => $subject->id,
        'marks_obtained' => 60,
        'is_absent' => false,
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

test('subject mark policy denies update when session is published', function () {
    $session = ExamSession::factory()->published()->create();
    $course = Course::factory()->create();
    $student = Student::factory()->create(['course_id' => $course->id]);
    $subject = Subject::factory()->forCourse($course)->create();
    ExamSessionSubject::create(['exam_session_id' => $session->id, 'subject_id' => $subject->id]);
    $mark = SubjectMark::create([
        'exam_session_id' => $session->id,
        'student_id' => $student->id,
        'subject_id' => $subject->id,
        'marks_obtained' => 50,
        'is_absent' => false,
    ]);
    $user = User::factory()->create();
    $user->givePermissionTo('subject-mark.update');

    $policy = new SubjectMarkPolicy;
    expect($policy->update($user, $mark))->toBeFalse();
});

test('cannot delete exam session that has exam forms', function () {
    $session = ExamSession::factory()->create();
    ExamForm::factory()->create(['exam_session_id' => $session->id]);

    expect(fn () => $session->delete())->toThrow(\Illuminate\Database\QueryException::class);
});

test('cannot delete exam session that has subject marks', function () {
    $session = ExamSession::factory()->create();
    SubjectMark::factory()->create(['exam_session_id' => $session->id]);

    expect(fn () => $session->delete())->toThrow(\Illuminate\Database\QueryException::class);
});

test('cannot delete student that has exam forms', function () {
    $student = Student::factory()->create();
    ExamForm::factory()->create(['student_id' => $student->id]);

    expect(fn () => $student->delete())->toThrow(\Illuminate\Database\QueryException::class);
});

test('cannot delete student that has subject marks', function () {
    $session = ExamSession::factory()->create();
    $student = Student::factory()->create();
    $subject = Subject::factory()->forCourse($student->course)->create();
    ExamSessionSubject::create(['exam_session_id' => $session->id, 'subject_id' => $subject->id]);
    SubjectMark::create([
        'exam_session_id' => $session->id,
        'student_id' => $student->id,
        'subject_id' => $subject->id,
        'marks_obtained' => 50,
        'is_absent' => false,
    ]);

    expect(fn () => $student->delete())->toThrow(\Illuminate\Database\QueryException::class);
});
