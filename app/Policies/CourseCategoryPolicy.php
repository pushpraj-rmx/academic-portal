<?php

namespace App\Policies;

use App\Models\CourseCategory;
use App\Models\User;

class CourseCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('category.view');
    }

    public function view(User $user, CourseCategory $courseCategory): bool
    {
        return $user->can('category.view');
    }

    public function create(User $user): bool
    {
        return $user->can('category.create');
    }

    public function update(User $user, CourseCategory $courseCategory): bool
    {
        return $user->can('category.update');
    }

    public function delete(User $user, CourseCategory $courseCategory): bool
    {
        return $user->can('category.delete');
    }

    public function restore(User $user, CourseCategory $courseCategory): bool
    {
        return false;
    }

    public function forceDelete(User $user, CourseCategory $courseCategory): bool
    {
        return false;
    }
}
