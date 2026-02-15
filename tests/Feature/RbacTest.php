<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('user can be assigned a role', function () {
    $user = User::factory()->create();

    $user->assignRole(UserRole::ContentManager->value);

    expect($user->hasRole(UserRole::ContentManager->value))->toBeTrue()
        ->and($user->getRoleNames()->toArray())->toContain(UserRole::ContentManager->value);
});

test('user with academic admin role has expected permissions', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::AcademicAdmin->value);

    expect($user->can('course.view'))->toBeTrue()
        ->and($user->can('course.create'))->toBeTrue()
        ->and($user->can('user.delete'))->toBeFalse();
});

test('super admin has all permissions via gate before', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::SuperAdmin->value);

    expect($user->can('user.view'))->toBeTrue()
        ->and($user->can('role.delete'))->toBeTrue()
        ->and($user->can('course.create'))->toBeTrue()
        ->and($user->can('result.publish'))->toBeTrue();
});

test('student has no admin permissions', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::Student->value);

    expect($user->can('user.view'))->toBeFalse()
        ->and($user->can('role.view'))->toBeFalse();
});

test('user can have multiple roles', function () {
    $user = User::factory()->create();
    $user->assignRole([UserRole::ExaminationCell->value, UserRole::ContentManager->value]);

    expect($user->hasRole(UserRole::ExaminationCell->value))->toBeTrue()
        ->and($user->hasRole(UserRole::ContentManager->value))->toBeTrue()
        ->and($user->getRoleNames())->toHaveCount(2);
});
