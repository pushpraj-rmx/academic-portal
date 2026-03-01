<?php

namespace App\Policies;

use App\Models\NavItem;
use App\Models\User;

class NavItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('nav-item.view');
    }

    public function view(User $user, NavItem $navItem): bool
    {
        return $user->can('nav-item.view');
    }

    public function create(User $user): bool
    {
        return $user->can('nav-item.create');
    }

    public function update(User $user, NavItem $navItem): bool
    {
        return $user->can('nav-item.update');
    }

    public function delete(User $user, NavItem $navItem): bool
    {
        return $user->can('nav-item.delete');
    }

    public function restore(User $user, NavItem $navItem): bool
    {
        return false;
    }

    public function forceDelete(User $user, NavItem $navItem): bool
    {
        return false;
    }
}
