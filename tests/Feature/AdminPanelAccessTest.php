<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('guest cannot access admin panel and is redirected to login', function () {
    $response = $this->get('/admin');

    $response->assertRedirect('/admin/login');
});

test('super admin can access admin panel', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::SuperAdmin->value);

    $response = $this->actingAs($user)->get('/admin');

    $response->assertSuccessful();
});

test('student cannot access admin panel', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::Student->value);

    $response = $this->actingAs($user)->get('/admin');

    $response->assertForbidden();
});

test('academic admin can access admin panel', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::AcademicAdmin->value);

    $response = $this->actingAs($user)->get('/admin');

    $response->assertSuccessful();
});

test('user with no role cannot access admin panel', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/admin');

    $response->assertForbidden();
});
