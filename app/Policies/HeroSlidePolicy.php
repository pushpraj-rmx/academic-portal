<?php

namespace App\Policies;

use App\Models\HeroSlide;
use App\Models\User;

class HeroSlidePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('hero-slide.view');
    }

    public function view(User $user, HeroSlide $heroSlide): bool
    {
        return $user->can('hero-slide.view');
    }

    public function create(User $user): bool
    {
        return $user->can('hero-slide.create');
    }

    public function update(User $user, HeroSlide $heroSlide): bool
    {
        return $user->can('hero-slide.update');
    }

    public function delete(User $user, HeroSlide $heroSlide): bool
    {
        return $user->can('hero-slide.delete');
    }

    public function restore(User $user, HeroSlide $heroSlide): bool
    {
        return false;
    }

    public function forceDelete(User $user, HeroSlide $heroSlide): bool
    {
        return false;
    }
}
