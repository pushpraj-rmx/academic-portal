<?php

namespace App\Policies;

use App\Models\Recruiter;
use App\Models\User;

class RecruiterPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('recruiter.view');
    }

    public function view(User $user, Recruiter $recruiter): bool
    {
        return $user->can('recruiter.view');
    }

    public function create(User $user): bool
    {
        return $user->can('recruiter.create');
    }

    public function update(User $user, Recruiter $recruiter): bool
    {
        return $user->can('recruiter.update');
    }

    public function delete(User $user, Recruiter $recruiter): bool
    {
        return $user->can('recruiter.delete');
    }

    public function restore(User $user, Recruiter $recruiter): bool
    {
        return false;
    }

    public function forceDelete(User $user, Recruiter $recruiter): bool
    {
        return false;
    }
}
