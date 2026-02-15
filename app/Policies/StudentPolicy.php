<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('student.view');
    }

    public function view(User $user, Student $student): bool
    {
        return $user->can('student.view');
    }

    public function create(User $user): bool
    {
        return $user->can('student.create');
    }

    public function update(User $user, Student $student): bool
    {
        return $user->can('student.update');
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->can('student.delete');
    }

    public function restore(User $user, Student $student): bool
    {
        return false;
    }

    public function forceDelete(User $user, Student $student): bool
    {
        return false;
    }

    public function verify(User $user, Student $student): bool
    {
        return $user->can('student.verify');
    }
}
