<?php

use App\Models\Course;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('student factory creates student with pending verification', function () {
    $student = Student::factory()->create();

    expect($student->verification_status)->toBe('pending');
    expect($student->verified_at)->toBeNull();
    expect($student->enrollment_id)->not->toBeEmpty();
});

test('enrollment_id is unique globally', function () {
    Student::factory()->create(['enrollment_id' => 'ENR001']);

    expect(fn () => Student::factory()->create(['enrollment_id' => 'ENR001']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

test('roll_number is unique per course', function () {
    $course = Course::factory()->create();
    Student::factory()->create(['course_id' => $course->id, 'roll_number' => 'R1']);

    expect(fn () => Student::factory()->create(['course_id' => $course->id, 'roll_number' => 'R1']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

test('same roll_number allowed in different courses', function () {
    $c1 = Course::factory()->create();
    $c2 = Course::factory()->create();
    Student::factory()->create(['course_id' => $c1->id, 'roll_number' => 'R1']);
    $student2 = Student::factory()->create(['course_id' => $c2->id, 'roll_number' => 'R1']);

    expect($student2->roll_number)->toBe('R1');
});

test('updating student to verified sets verified_at and verified_by', function () {
    $user = User::factory()->create();
    $student = Student::factory()->create(['verification_status' => 'pending']);

    $student->update([
        'verification_status' => 'verified',
        'verified_at' => now(),
        'verified_by' => $user->id,
    ]);

    $student->refresh();
    expect($student->verification_status)->toBe('verified');
    expect($student->verified_at)->not->toBeNull();
    expect($student->verified_by)->toBe($user->id);
});

test('updating student to rejected clears verified_at', function () {
    $student = Student::factory()->verified()->create();

    $student->update(['verification_status' => 'rejected', 'verified_at' => null, 'verified_by' => null]);

    $student->refresh();
    expect($student->verification_status)->toBe('rejected');
    expect($student->verified_at)->toBeNull();
    expect($student->verified_by)->toBeNull();
});

test('document download requires authentication', function () {
    $document = StudentDocument::factory()->create();

    $response = $this->get(route('admin.student-documents.download', $document));

    $response->assertRedirect();
});

test('document download requires student-document.view permission', function () {
    $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
    Storage::fake('local');
    Storage::disk('local')->put('student-documents/test.pdf', '%PDF-1.4 fake');

    $document = StudentDocument::factory()->create(['file_path' => 'student-documents/test.pdf']);
    $user = User::factory()->create();
    $user->assignRole(\App\Enums\UserRole::Student->value);

    $response = $this->actingAs($user)->get(route('admin.student-documents.download', $document));

    $response->assertForbidden();
});

test('document download returns file when user has permission', function () {
    $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
    Storage::fake('local');
    Storage::disk('local')->put('student-documents/test.pdf', '%PDF-1.4 fake');

    $document = StudentDocument::factory()->create(['file_path' => 'student-documents/test.pdf']);
    $user = User::factory()->create();
    $user->assignRole(\App\Enums\UserRole::AcademicAdmin->value);

    $response = $this->actingAs($user)->get(route('admin.student-documents.download', $document));

    $response->assertSuccessful();
});

test('deleting course with students fails with restrict', function () {
    $course = Course::factory()->create();
    Student::factory()->create(['course_id' => $course->id]);

    expect(fn () => $course->delete())->toThrow(\Illuminate\Database\QueryException::class);
});

test('deleting student with documents fails with restrict', function () {
    $student = Student::factory()->create();
    StudentDocument::factory()->create(['student_id' => $student->id]);

    expect(fn () => $student->delete())->toThrow(\Illuminate\Database\QueryException::class);
});
