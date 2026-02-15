<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('academic admin can access course category resource index', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::AcademicAdmin->value);

    $response = $this->actingAs($user)->get('/admin/course-categories');

    $response->assertSuccessful();
});

test('academic admin can access course resource index', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::AcademicAdmin->value);

    $response = $this->actingAs($user)->get('/admin/courses');

    $response->assertSuccessful();
});

test('academic admin can access specialization resource index', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::AcademicAdmin->value);

    $response = $this->actingAs($user)->get('/admin/specializations');

    $response->assertSuccessful();
});

test('academic admin can access syllabus resource index', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::AcademicAdmin->value);

    $response = $this->actingAs($user)->get('/admin/syllabi');

    $response->assertSuccessful();
});

test('student cannot access course category resource', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::Student->value);

    $response = $this->actingAs($user)->get('/admin/course-categories');

    $response->assertForbidden();
});

test('student cannot access course resource', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::Student->value);

    $response = $this->actingAs($user)->get('/admin/courses');

    $response->assertForbidden();
});
