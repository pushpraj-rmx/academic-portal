<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('content manager can access announcement resource index', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::ContentManager->value);

    $response = $this->actingAs($user)->get('/admin/announcements');

    $response->assertSuccessful();
});

test('content manager can access announcement create', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::ContentManager->value);

    $response = $this->actingAs($user)->get('/admin/announcements/create');

    $response->assertSuccessful();
});

test('student cannot access announcement resource', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::Student->value);

    $response = $this->actingAs($user)->get('/admin/announcements');

    $response->assertForbidden();
});
