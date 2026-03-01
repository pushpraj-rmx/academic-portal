<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('guest visiting student dashboard is redirected to login', function () {
    $response = $this->get(route('student.dashboard'));

    $response->assertRedirect(route('login'));
});

test('student can access student dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::Student->value);

    $response = $this->actingAs($user)->get(route('student.dashboard'));

    $response->assertSuccessful();
    $response->assertSee('Student dashboard', false);
});

test('non-student is redirected to admin when visiting student dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::AcademicAdmin->value);

    $response = $this->actingAs($user)->get(route('student.dashboard'));

    $response->assertRedirect(route('filament.admin.pages.dashboard'));
});

test('guest visiting admin is redirected to login', function () {
    $response = $this->get('/admin');

    $response->assertRedirect(route('login'));
});
