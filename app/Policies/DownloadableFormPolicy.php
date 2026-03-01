<?php

namespace App\Policies;

use App\Models\DownloadableForm;
use App\Models\User;

class DownloadableFormPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('downloadable-form.view');
    }

    public function view(User $user, DownloadableForm $downloadableForm): bool
    {
        return $user->can('downloadable-form.view');
    }

    public function create(User $user): bool
    {
        return $user->can('downloadable-form.create');
    }

    public function update(User $user, DownloadableForm $downloadableForm): bool
    {
        return $user->can('downloadable-form.update');
    }

    public function delete(User $user, DownloadableForm $downloadableForm): bool
    {
        return $user->can('downloadable-form.delete');
    }

    public function restore(User $user, DownloadableForm $downloadableForm): bool
    {
        return false;
    }

    public function forceDelete(User $user, DownloadableForm $downloadableForm): bool
    {
        return false;
    }
}
