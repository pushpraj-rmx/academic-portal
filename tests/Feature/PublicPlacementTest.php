<?php

use App\Models\Course;
use App\Models\Placement;
use App\Models\Recruiter;
use App\Models\Student;

test('recruiters page shows only active recruiters', function () {
    Recruiter::factory()->create(['name' => 'Active Corp', 'is_active' => true]);
    Recruiter::factory()->inactive()->create(['name' => 'Inactive Corp']);

    $response = $this->get(route('placements.recruiters'));

    $response->assertSuccessful();
    $response->assertSee('Active Corp');
    $response->assertDontSee('Inactive Corp');
});

test('recruiters page ordered by sort_order', function () {
    Recruiter::factory()->create(['name' => 'Second', 'sort_order' => 10]);
    Recruiter::factory()->create(['name' => 'First', 'sort_order' => 0]);

    $response = $this->get(route('placements.recruiters'));

    $response->assertSuccessful();
    $body = $response->getContent();
    $posFirst = strpos($body, 'First');
    $posSecond = strpos($body, 'Second');
    expect($posFirst)->toBeLessThan($posSecond);
});

test('statistics page returns aggregated data', function () {
    $course = Course::factory()->create(['name' => 'B.Tech CSE']);
    $student = Student::factory()->create(['course_id' => $course->id]);
    $recruiter1 = Recruiter::factory()->create();
    $recruiter2 = Recruiter::factory()->create();
    Placement::factory()->create([
        'student_id' => $student->id,
        'recruiter_id' => $recruiter1->id,
        'academic_year' => '2024-2025',
        'status' => 'joined',
        'package_amount' => 12.5,
    ]);
    Placement::factory()->create([
        'student_id' => $student->id,
        'recruiter_id' => $recruiter2->id,
        'academic_year' => '2024-2025',
        'status' => 'offered',
    ]);

    $response = $this->get(route('placements.statistics'));

    $response->assertSuccessful();
    $response->assertSee('2024-2025');
    $response->assertSee('2');
    $response->assertSee('B.Tech CSE');
    $response->assertSee('12.50');
});

test('statistics page does not expose student names', function () {
    $student = Student::factory()->create();
    $student->load('user');
    Placement::factory()->create(['student_id' => $student->id]);

    $response = $this->get(route('placements.statistics'));

    $response->assertSuccessful();
    $response->assertDontSee($student->user?->name ?? '');
    $response->assertDontSee($student->enrollment_id);
});
