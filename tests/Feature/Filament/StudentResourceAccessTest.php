<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('academic admin can access student resource index', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::AcademicAdmin->value);

    $response = $this->actingAs($user)->get('/admin/students');

    $response->assertSuccessful();
});

test('examination cell can access student resource index', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::ExaminationCell->value);

    $response = $this->actingAs($user)->get('/admin/students');

    $response->assertSuccessful();
});

test('student role cannot access student resource', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::Student->value);

    $response = $this->actingAs($user)->get('/admin/students');

    $response->assertForbidden();
});
