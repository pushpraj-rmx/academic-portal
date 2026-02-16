<?php

namespace App\Policies;

use App\Models\Placement;
use App\Models\User;

class PlacementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('placement.view');
    }

    public function view(User $user, Placement $placement): bool
    {
        return $user->can('placement.view');
    }

    public function create(User $user): bool
    {
        return $user->can('placement.create');
    }

    public function update(User $user, Placement $placement): bool
    {
        return $user->can('placement.update');
    }

    public function delete(User $user, Placement $placement): bool
    {
        return $user->can('placement.delete');
    }

    public function restore(User $user, Placement $placement): bool
    {
        return false;
    }

    public function forceDelete(User $user, Placement $placement): bool
    {
        return false;
    }
}
