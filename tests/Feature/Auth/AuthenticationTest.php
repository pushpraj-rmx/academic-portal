<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('login page shows form', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('Log in', false);
});

test('student user redirects to student dashboard after login', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::Student->value);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('student.dashboard'));
});

test('admin role user redirects to admin after login', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::AcademicAdmin->value);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('filament.admin.pages.dashboard'));
});

test('invalid credentials do not authenticate', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::Student->value);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors('email');
});

test('users can logout and are redirected to login', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::Student->value);

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect(route('login'));
});

test('intended redirect after login when guest visited admin', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::AcademicAdmin->value);

    $this->get('/admin');
    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('filament.admin.pages.dashboard'));
});

test('intended redirect after login when guest visited student', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::Student->value);

    $this->get('/student');
    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('student.dashboard'));
});
