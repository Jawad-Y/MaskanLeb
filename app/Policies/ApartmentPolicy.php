<?php

namespace App\Policies;

use App\Models\Apartment;
use App\Models\User;

class ApartmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Apartment $apartment): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isOwner() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Apartment $apartment): bool
    {
        return $user->id === $apartment->owner_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Apartment $apartment): bool
    {
        return $user->id === $apartment->owner_id || $user->isAdmin();
    }
}
