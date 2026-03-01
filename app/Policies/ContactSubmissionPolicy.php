<?php

namespace App\Policies;

use App\Models\ContactSubmission;
use App\Models\User;

class ContactSubmissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('contact-submission.view');
    }

    public function view(User $user, ContactSubmission $contactSubmission): bool
    {
        return $user->can('contact-submission.view');
    }

    public function create(User $user): bool
    {
        return $user->can('contact-submission.create');
    }

    public function update(User $user, ContactSubmission $contactSubmission): bool
    {
        return $user->can('contact-submission.update');
    }

    public function delete(User $user, ContactSubmission $contactSubmission): bool
    {
        return $user->can('contact-submission.delete');
    }

    public function restore(User $user, ContactSubmission $contactSubmission): bool
    {
        return false;
    }

    public function forceDelete(User $user, ContactSubmission $contactSubmission): bool
    {
        return false;
    }
}
