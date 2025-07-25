<?php

namespace App\Policies;

use App\Models\ContactTag;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ContactTagPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ContactTag $contactTag): bool
    {
        return $user->id === $contactTag->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ContactTag $contactTag): bool
    {
        return $user->id === $contactTag->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ContactTag $contactTag): bool
    {
        return $user->id === $contactTag->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ContactTag $contactTag): bool
    {
        return $user->id === $contactTag->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ContactTag $contactTag): bool
    {
        return $user->id === $contactTag->user_id;
    }
}
