<?php

namespace App\Policies;

use App\Models\ExamSession;
use App\Models\User;

class ExamSessionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('exam-session.view');
    }

    public function view(User $user, ExamSession $examSession): bool
    {
        return $user->can('exam-session.view');
    }

    public function create(User $user): bool
    {
        return $user->can('exam-session.create');
    }

    public function update(User $user, ExamSession $examSession): bool
    {
        return $user->can('exam-session.update');
    }

    public function delete(User $user, ExamSession $examSession): bool
    {
        return $user->can('exam-session.delete');
    }

    public function restore(User $user, ExamSession $examSession): bool
    {
        return false;
    }

    public function forceDelete(User $user, ExamSession $examSession): bool
    {
        return false;
    }
}
