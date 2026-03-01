<?php

namespace App\Policies;

use App\Models\Certification;
use App\Models\User;

class CertificationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('certification.view');
    }

    public function view(User $user, Certification $certification): bool
    {
        return $user->can('certification.view');
    }

    public function create(User $user): bool
    {
        return $user->can('certification.create');
    }

    public function update(User $user, Certification $certification): bool
    {
        return $user->can('certification.update');
    }

    public function delete(User $user, Certification $certification): bool
    {
        return $user->can('certification.delete');
    }

    public function restore(User $user, Certification $certification): bool
    {
        return false;
    }

    public function forceDelete(User $user, Certification $certification): bool
    {
        return false;
    }
}
