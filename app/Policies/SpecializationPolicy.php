<?php

namespace App\Policies;

use App\Models\Specialization;
use App\Models\User;

class SpecializationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('specialization.view');
    }

    public function view(User $user, Specialization $specialization): bool
    {
        return $user->can('specialization.view');
    }

    public function create(User $user): bool
    {
        return $user->can('specialization.create');
    }

    public function update(User $user, Specialization $specialization): bool
    {
        return $user->can('specialization.update');
    }

    public function delete(User $user, Specialization $specialization): bool
    {
        return $user->can('specialization.delete');
    }

    public function restore(User $user, Specialization $specialization): bool
    {
        return false;
    }

    public function forceDelete(User $user, Specialization $specialization): bool
    {
        return false;
    }
}
