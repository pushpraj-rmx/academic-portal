<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('user with user.view permission can access user resource index', function () {
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::SuperAdmin->value);

    $response = $this->actingAs($admin)->get('/admin/users');

    $response->assertSuccessful();
});

test('user without user.view permission cannot access user resource index', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::Student->value);

    $response = $this->actingAs($user)->get('/admin/users');

    $response->assertForbidden();
});

test('user with role.view permission can access role resource index', function () {
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::SuperAdmin->value);

    $response = $this->actingAs($admin)->get('/admin/roles');

    $response->assertSuccessful();
});

test('academic admin without role permission cannot access role resource', function () {
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::AcademicAdmin->value);

    $response = $this->actingAs($admin)->get('/admin/roles');

    $response->assertForbidden();
});

test('super admin can access user create page', function () {
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::SuperAdmin->value);

    $response = $this->actingAs($admin)->get('/admin/users/create');

    $response->assertSuccessful();
});
