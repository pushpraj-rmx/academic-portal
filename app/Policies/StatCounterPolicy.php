<?php

namespace App\Policies;

use App\Models\StatCounter;
use App\Models\User;

class StatCounterPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('stat-counter.view');
    }

    public function view(User $user, StatCounter $statCounter): bool
    {
        return $user->can('stat-counter.view');
    }

    public function create(User $user): bool
    {
        return $user->can('stat-counter.create');
    }

    public function update(User $user, StatCounter $statCounter): bool
    {
        return $user->can('stat-counter.update');
    }

    public function delete(User $user, StatCounter $statCounter): bool
    {
        return $user->can('stat-counter.delete');
    }

    public function restore(User $user, StatCounter $statCounter): bool
    {
        return false;
    }

    public function forceDelete(User $user, StatCounter $statCounter): bool
    {
        return false;
    }
}
