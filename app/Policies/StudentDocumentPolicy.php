<?php

namespace App\Policies;

use App\Models\StudentDocument;
use App\Models\User;

class StudentDocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('student-document.view');
    }

    public function view(User $user, StudentDocument $studentDocument): bool
    {
        return $user->can('student-document.view');
    }

    public function create(User $user): bool
    {
        return $user->can('student-document.create');
    }

    public function update(User $user, StudentDocument $studentDocument): bool
    {
        return $user->can('student-document.view');
    }

    public function delete(User $user, StudentDocument $studentDocument): bool
    {
        return $user->can('student-document.delete');
    }

    public function restore(User $user, StudentDocument $studentDocument): bool
    {
        return false;
    }

    public function forceDelete(User $user, StudentDocument $studentDocument): bool
    {
        return false;
    }
}
