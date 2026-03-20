<?php

use App\Models\Course;
use App\Models\ExamSession;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectMark;

test('SubjectMark::existsFor returns true for an existing composite key', function () {
    $session = ExamSession::factory()->create();
    $course = Course::factory()->create();
    $student = Student::factory()->create(['course_id' => $course->id]);
    $subject = Subject::factory()->forCourse($course)->create();

    SubjectMark::create([
        'exam_session_id' => $session->id,
        'student_id' => $student->id,
        'subject_id' => $subject->id,
        'marks_obtained' => 50,
        'is_absent' => false,
    ]);

    expect(SubjectMark::existsFor($session->id, $student->id, $subject->id))->toBeTrue();
    expect(SubjectMark::existsFor($session->id, $student->id, $subject->id + 9999))->toBeFalse();
});

test('SubjectMark::subjectIdsForStudentInSession returns the correct subject ids', function () {
    $session = ExamSession::factory()->create();
    $course = Course::factory()->create();
    $student = Student::factory()->create(['course_id' => $course->id]);
    $sub1 = Subject::factory()->forCourse($course)->create();
    $sub2 = Subject::factory()->forCourse($course)->create();

    SubjectMark::create([
        'exam_session_id' => $session->id,
        'student_id' => $student->id,
        'subject_id' => $sub1->id,
        'marks_obtained' => 60,
        'is_absent' => false,
    ]);

    SubjectMark::create([
        'exam_session_id' => $session->id,
        'student_id' => $student->id,
        'subject_id' => $sub2->id,
        'marks_obtained' => 70,
        'is_absent' => false,
    ]);

    $ids = SubjectMark::subjectIdsForStudentInSession($session->id, $student->id);

    expect($ids)->toHaveCount(2);
    expect($ids)->toContain($sub1->id);
    expect($ids)->toContain($sub2->id);
});
