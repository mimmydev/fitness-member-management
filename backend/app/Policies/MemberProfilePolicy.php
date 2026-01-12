<?php

namespace App\Policies;

use App\Models\MemberProfile;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MemberProfilePolicy
{
    /**
     * Determine whether the user can view any models.
     * All authenticated users can view the member list.
     */
    public function viewAny(User $user): bool
    {
        return true; // Authenticated users can see member list
    }

    /**
     * Determine whether the user can view the model.
     * Users can only view their own member profile.
     */
    public function view(User $user, MemberProfile $memberProfile): bool
    {
        // User can only view their own profile
        return $user->id === $memberProfile->user_id;
    }

    /**
     * Determine whether the user can create models.
     * All authenticated users can create their own member profile.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can create a profile
    }

    /**
     * Determine whether the user can update the model.
     * Users can only update their own member profile.
     */
    public function update(User $user, MemberProfile $memberProfile): bool
    {
        // User can only update their own profile
        return $user->id === $memberProfile->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     * Users can only delete their own member profile.
     */
    public function delete(User $user, MemberProfile $memberProfile): bool
    {
        // User can only delete their own profile
        return $user->id === $memberProfile->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     * Users can only restore their own soft-deleted profile.
     */
    public function restore(User $user, MemberProfile $memberProfile): bool
    {
        // User can only restore their own profile
        return $user->id === $memberProfile->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     * Users cannot permanently delete (only soft delete allowed).
     */
    public function forceDelete(User $user, MemberProfile $memberProfile): bool
    {
        // Nobody can permanently delete - only soft delete
        return false;
    }
}
