<?php

namespace App\Policies;

use App\Models\ExamForm;
use App\Models\User;

class ExamFormPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('exam-form.view');
    }

    public function view(User $user, ExamForm $examForm): bool
    {
        return $user->can('exam-form.view');
    }

    public function create(User $user): bool
    {
        return $user->can('exam-form.create');
    }

    public function update(User $user, ExamForm $examForm): bool
    {
        if ($examForm->examSession->status === 'published') {
            return false;
        }

        return $user->can('exam-form.update');
    }

    public function delete(User $user, ExamForm $examForm): bool
    {
        if ($examForm->examSession->status === 'published') {
            return false;
        }

        return $user->can('exam-form.delete');
    }

    public function restore(User $user, ExamForm $examForm): bool
    {
        return false;
    }

    public function forceDelete(User $user, ExamForm $examForm): bool
    {
        return false;
    }
}
