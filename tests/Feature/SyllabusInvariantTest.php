<?php

use App\Models\Course;
use App\Models\Syllabus;

test('activating a syllabus deactivates other syllabi for the same course', function () {
    $course = Course::factory()->create();
    $first = Syllabus::factory()->create([
        'course_id' => $course->id,
        'is_active' => true,
    ]);
    $second = Syllabus::factory()->create([
        'course_id' => $course->id,
        'is_active' => false,
    ]);

    $second->update(['is_active' => true]);

    expect($first->fresh()->is_active)->toBeFalse();
    expect($second->fresh()->is_active)->toBeTrue();
});

test('saving a syllabus with is_active true deactivates other syllabi for same course', function () {
    $course = Course::factory()->create();
    Syllabus::factory()->create([
        'course_id' => $course->id,
        'is_active' => true,
    ]);
    $newActive = Syllabus::factory()->create([
        'course_id' => $course->id,
        'is_active' => false,
    ]);

    $newActive->is_active = true;
    $newActive->save();

    $activeCount = Syllabus::where('course_id', $course->id)->where('is_active', true)->count();
    expect($activeCount)->toBe(1);
});
