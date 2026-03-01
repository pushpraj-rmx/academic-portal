<?php

namespace App\Policies;

use App\Models\GradingRule;
use App\Models\User;

class GradingRulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('grading-rule.view');
    }

    public function view(User $user, GradingRule $gradingRule): bool
    {
        return $user->can('grading-rule.view');
    }

    public function create(User $user): bool
    {
        return $user->can('grading-rule.create');
    }

    public function update(User $user, GradingRule $gradingRule): bool
    {
        return $user->can('grading-rule.update');
    }

    public function delete(User $user, GradingRule $gradingRule): bool
    {
        return $user->can('grading-rule.delete');
    }

    public function restore(User $user, GradingRule $gradingRule): bool
    {
        return false;
    }

    public function forceDelete(User $user, GradingRule $gradingRule): bool
    {
        return false;
    }
}
