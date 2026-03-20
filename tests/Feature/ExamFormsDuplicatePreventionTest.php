<?php

use App\Models\Course;
use App\Models\ExamForm;
use App\Models\ExamSession;
use App\Models\ExamSessionSubject;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Validation\Rule;

test('duplicate exam form per (session, student) is prevented by scoped uniqueness rule', function () {
    $session = ExamSession::factory()->create(['status' => 'registration_open']);
    $course = Course::factory()->create();

    $student = Student::factory()->create(['course_id' => $course->id]);
    $subject = Subject::factory()->forCourse($course)->create();
    ExamSessionSubject::create([
        'exam_session_id' => $session->id,
        'subject_id' => $subject->id,
    ]);

    ExamForm::factory()->create([
        'exam_session_id' => $session->id,
        'student_id' => $student->id,
        'status' => 'applied',
    ]);

    $rule = Rule::unique('exam_forms', 'student_id')
        ->where(fn ($query) => $query->where('exam_session_id', $session->id));

    $validator = validator(
        ['student_id' => $student->id],
        ['student_id' => [$rule]],
    );

    expect($validator->fails())->toBeTrue();
});

test('student dropdown query excludes students who already have exam form for this session', function () {
    $session = ExamSession::factory()->create(['status' => 'registration_open']);
    $course = Course::factory()->create();

    $student = Student::factory()->create(['course_id' => $course->id]);
    $subject = Subject::factory()->forCourse($course)->create();
    ExamSessionSubject::create([
        'exam_session_id' => $session->id,
        'subject_id' => $subject->id,
    ]);

    ExamForm::factory()->create([
        'exam_session_id' => $session->id,
        'student_id' => $student->id,
        'status' => 'applied',
    ]);

    $sessionSubjectCourseIds = $session->examSessionSubjects()
        ->with('subject:id,course_id')
        ->get()
        ->pluck('subject.course_id')
        ->unique()
        ->filter()
        ->values()
        ->all();

    $query = Student::query()
        ->whereIn('course_id', $sessionSubjectCourseIds)
        ->whereDoesntHave('examForms', fn ($q) => $q->where('exam_session_id', $session->id));

    expect($query->pluck('id'))->not->toContain($student->id);
});
