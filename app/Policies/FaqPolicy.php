<?php

namespace App\Policies;

use App\Models\Faq;
use App\Models\User;

class FaqPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('faq.view');
    }

    public function view(User $user, Faq $faq): bool
    {
        return $user->can('faq.view');
    }

    public function create(User $user): bool
    {
        return $user->can('faq.create');
    }

    public function update(User $user, Faq $faq): bool
    {
        return $user->can('faq.update');
    }

    public function delete(User $user, Faq $faq): bool
    {
        return $user->can('faq.delete');
    }

    public function restore(User $user, Faq $faq): bool
    {
        return false;
    }

    public function forceDelete(User $user, Faq $faq): bool
    {
        return false;
    }
}
