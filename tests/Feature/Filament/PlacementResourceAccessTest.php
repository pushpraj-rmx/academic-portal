<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('placement cell can access recruiter resource index', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::PlacementCell->value);

    $response = $this->actingAs($user)->get('/admin/recruiters');

    $response->assertSuccessful();
});

test('placement cell can access placement resource index', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::PlacementCell->value);

    $response = $this->actingAs($user)->get('/admin/placements');

    $response->assertSuccessful();
});

test('student role cannot access recruiter resource', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::Student->value);

    $response = $this->actingAs($user)->get('/admin/recruiters');

    $response->assertForbidden();
});

test('student role cannot access placement resource', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::Student->value);

    $response = $this->actingAs($user)->get('/admin/placements');

    $response->assertForbidden();
});
