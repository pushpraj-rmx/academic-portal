<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('content manager can access page resource index', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::ContentManager->value);

    $response = $this->actingAs($user)->get('/admin/pages');

    $response->assertSuccessful();
});

test('content manager can access page create', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::ContentManager->value);

    $response = $this->actingAs($user)->get('/admin/pages/create');

    $response->assertSuccessful();
});

test('student cannot access page resource', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::Student->value);

    $response = $this->actingAs($user)->get('/admin/pages');

    $response->assertForbidden();
});
