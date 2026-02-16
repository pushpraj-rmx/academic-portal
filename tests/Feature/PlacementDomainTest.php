<?php

use App\Models\Placement;
use App\Models\Recruiter;
use App\Models\Student;

test('recruiter slug is unique', function () {
    Recruiter::factory()->create(['slug' => 'acme-corp']);

    expect(fn () => Recruiter::factory()->create(['slug' => 'acme-corp']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

test('placement uniqueness per student recruiter academic year', function () {
    $student = Student::factory()->create();
    $recruiter = Recruiter::factory()->create();
    Placement::factory()->create([
        'student_id' => $student->id,
        'recruiter_id' => $recruiter->id,
        'academic_year' => '2025-2026',
    ]);

    expect(fn () => Placement::factory()->create([
        'student_id' => $student->id,
        'recruiter_id' => $recruiter->id,
        'academic_year' => '2025-2026',
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

test('cannot delete recruiter with placements', function () {
    $recruiter = Recruiter::factory()->create();
    Placement::factory()->create(['recruiter_id' => $recruiter->id]);

    expect(fn () => $recruiter->delete())->toThrow(\Illuminate\Database\QueryException::class);
});

test('cannot delete student with placements', function () {
    $student = Student::factory()->create();
    Placement::factory()->create(['student_id' => $student->id]);

    expect(fn () => $student->delete())->toThrow(\Illuminate\Database\QueryException::class);
});

test('placement status workflow offered to joined', function () {
    $placement = Placement::factory()->create(['status' => 'offered']);

    $placement->update(['status' => 'joined']);
    expect($placement->fresh()->status)->toBe('joined');
});

test('placement status workflow offered to declined', function () {
    $placement = Placement::factory()->create(['status' => 'offered']);

    $placement->update(['status' => 'declined']);
    expect($placement->fresh()->status)->toBe('declined');
});

test('placement factory defaults to offered status', function () {
    $placement = Placement::factory()->create();

    expect($placement->status)->toBe('offered');
});
