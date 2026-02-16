<?php

namespace App\Policies;

use App\Models\SubjectMark;
use App\Models\User;

class SubjectMarkPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('subject-mark.view');
    }

    public function view(User $user, SubjectMark $subjectMark): bool
    {
        return $user->can('subject-mark.view');
    }

    public function create(User $user): bool
    {
        return $user->can('subject-mark.create');
    }

    public function update(User $user, SubjectMark $subjectMark): bool
    {
        if ($subjectMark->examSession->status === 'published') {
            return false;
        }

        return $user->can('subject-mark.update');
    }

    public function delete(User $user, SubjectMark $subjectMark): bool
    {
        return false;
    }

    public function restore(User $user, SubjectMark $subjectMark): bool
    {
        return false;
    }

    public function forceDelete(User $user, SubjectMark $subjectMark): bool
    {
        return false;
    }
}
