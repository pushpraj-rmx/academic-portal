<?php

use App\Models\Course;
use App\Models\Subject;

test('subject factory creates subject with required attributes', function () {
    $subject = Subject::factory()->create();

    expect($subject->name)->not->toBeEmpty();
    expect($subject->code)->not->toBeEmpty();
    expect($subject->max_marks)->toBeGreaterThan(0);
    expect($subject->passing_marks)->toBeGreaterThanOrEqual(0);
    expect($subject->course_id)->not->toBeNull();
    expect($subject->is_active)->toBeTrue();
});

test('subject code is unique per course', function () {
    $course = Course::factory()->create();
    Subject::factory()->forCourse($course)->create(['code' => 'CS101']);

    expect(fn () => Subject::factory()->forCourse($course)->create(['code' => 'CS101']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

test('same code allowed in different courses', function () {
    $c1 = Course::factory()->create();
    $c2 = Course::factory()->create();
    Subject::factory()->forCourse($c1)->create(['code' => 'CS101']);
    $s2 = Subject::factory()->forCourse($c2)->create(['code' => 'CS101']);

    expect($s2->code)->toBe('CS101');
});

test('cannot delete course that has subjects', function () {
    $course = Course::factory()->create();
    Subject::factory()->forCourse($course)->create();

    expect(fn () => $course->delete())->toThrow(\Illuminate\Database\QueryException::class);
});
