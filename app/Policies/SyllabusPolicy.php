<?php

namespace App\Policies;

use App\Models\Syllabus;
use App\Models\User;

class SyllabusPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('syllabus.view');
    }

    public function view(User $user, Syllabus $syllabus): bool
    {
        return $user->can('syllabus.view');
    }

    public function create(User $user): bool
    {
        return $user->can('syllabus.create');
    }

    public function update(User $user, Syllabus $syllabus): bool
    {
        return $user->can('syllabus.update');
    }

    public function delete(User $user, Syllabus $syllabus): bool
    {
        return $user->can('syllabus.delete');
    }

    public function restore(User $user, Syllabus $syllabus): bool
    {
        return false;
    }

    public function forceDelete(User $user, Syllabus $syllabus): bool
    {
        return false;
    }
}
