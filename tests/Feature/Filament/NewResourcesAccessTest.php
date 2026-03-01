<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('content manager can access new content resources', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::ContentManager->value);

    $this->actingAs($user)->get('/admin/hero-slides')->assertSuccessful();
    $this->actingAs($user)->get('/admin/stat-counters')->assertSuccessful();
    $this->actingAs($user)->get('/admin/certifications')->assertSuccessful();
    $this->actingAs($user)->get('/admin/faqs')->assertSuccessful();
    $this->actingAs($user)->get('/admin/downloadable-forms')->assertSuccessful();
    $this->actingAs($user)->get('/admin/contact-submissions')->assertSuccessful();
});

test('examination cell can access grading rules resource', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::ExaminationCell->value);

    $response = $this->actingAs($user)->get('/admin/grading-rules');

    $response->assertSuccessful();
});

test('student cannot access new resources', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::Student->value);

    $response = $this->actingAs($user)->get('/admin/hero-slides');

    $response->assertForbidden();
});
