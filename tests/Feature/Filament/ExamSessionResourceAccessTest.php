<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('examination cell can access exam session resource index', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::ExaminationCell->value);

    $response = $this->actingAs($user)->get('/admin/exam-sessions');

    $response->assertSuccessful();
});

test('student role cannot access exam session resource', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::Student->value);

    $response = $this->actingAs($user)->get('/admin/exam-sessions');

    $response->assertForbidden();
});
