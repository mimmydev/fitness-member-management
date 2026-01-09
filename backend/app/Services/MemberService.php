<?php

namespace App\Services;

use App\Models\MemberProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * MemberService
 *
 * Handles all member profile business logic.
 * Implements business rules and transactions.
 * Controllers delegate to this service for member operations.
 */
class MemberService
{
    /**
     * Get all member profiles with user data.
     *
     * Eager loads user relationship to prevent N+1 queries.
     * Returns only non-deleted members (soft deletes).
     *
     * @return Collection<MemberProfile>
     */
    public function getAllMembers(): Collection
    {
        return MemberProfile::with('user')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }

    /**
     * Get active members only.
     *
     * @return Collection<MemberProfile>
     */
    public function getActiveMembers(): Collection
    {
        return MemberProfile::with('user')
            ->active()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }

    /**
     * Find a member profile by ID.
     *
     * Uses findOrFail for automatic 404 handling.
     *
     * @param int $id Member profile ID
     * @return MemberProfile
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findMemberById(int $id): MemberProfile
    {
        return MemberProfile::with('user')->findOrFail($id);
    }

    /**
     * Create a new member profile for a user.
     *
     * Uses database transaction to ensure data consistency.
     * If any operation fails, entire transaction is rolled back.
     *
     * @param User $user The user to create profile for
     * @param array $data Validated member data
     * @return MemberProfile
     * @throws \Throwable
     */
    public function createMemberProfile(User $user, array $data): MemberProfile
    {
        return DB::transaction(function () use ($user, $data) {
            // Check if user already has a profile
            if ($user->hasMemberProfile()) {
                throw new \DomainException('User already has a member profile.');
            }

            // Create member profile
            $memberProfile = MemberProfile::create([
                'user_id' => $user->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender' => $data['gender'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'membership_start_date' => $data['membership_start_date'] ?? now(),
                'membership_end_date' => $data['membership_end_date'] ?? null,
                'membership_status' => $data['membership_status'] ?? 'active',
                'membership_type' => $data['membership_type'] ?? 'basic',
                'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
            ]);

            return $memberProfile->load('user');
        });
    }

    /**
     * Update an existing member profile.
     *
     * Uses database transaction for data consistency.
     *
     * @param int $id Member profile ID
     * @param array $data Validated member data
     * @return MemberProfile
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function updateMemberProfile(int $id, array $data): MemberProfile
    {
        return DB::transaction(function () use ($id, $data) {
            $memberProfile = MemberProfile::findOrFail($id);

            $memberProfile->update([
                'first_name' => $data['first_name'] ?? $memberProfile->first_name,
                'last_name' => $data['last_name'] ?? $memberProfile->last_name,
                'phone' => $data['phone'] ?? $memberProfile->phone,
                'date_of_birth' => $data['date_of_birth'] ?? $memberProfile->date_of_birth,
                'gender' => $data['gender'] ?? $memberProfile->gender,
                'address' => $data['address'] ?? $memberProfile->address,
                'city' => $data['city'] ?? $memberProfile->city,
                'state' => $data['state'] ?? $memberProfile->state,
                'postal_code' => $data['postal_code'] ?? $memberProfile->postal_code,
                'membership_start_date' => $data['membership_start_date'] ?? $memberProfile->membership_start_date,
                'membership_end_date' => $data['membership_end_date'] ?? $memberProfile->membership_end_date,
                'membership_status' => $data['membership_status'] ?? $memberProfile->membership_status,
                'membership_type' => $data['membership_type'] ?? $memberProfile->membership_type,
                'emergency_contact_name' => $data['emergency_contact_name'] ?? $memberProfile->emergency_contact_name,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? $memberProfile->emergency_contact_phone,
            ]);

            return $memberProfile->load('user');
        });
    }

    /**
     * Soft delete a member profile.
     *
     * Preserves data for audit trail and potential recovery.
     * Does NOT delete the associated user account.
     *
     * @param int $id Member profile ID
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function deleteMemberProfile(int $id): bool
    {
        $memberProfile = MemberProfile::findOrFail($id);

        return $memberProfile->delete();
    }

    /**
     * Restore a soft-deleted member profile.
     *
     * @param int $id Member profile ID
     * @return MemberProfile
     */
    public function restoreMemberProfile(int $id): MemberProfile
    {
        $memberProfile = MemberProfile::withTrashed()->findOrFail($id);
        $memberProfile->restore();

        return $memberProfile->load('user');
    }

    /**
     * Get members with expiring memberships.
     *
     * Useful for sending renewal reminders.
     *
     * @param int $days Number of days to check (default: 30)
     * @return Collection<MemberProfile>
     */
    public function getExpiringMemberships(int $days = 30): Collection
    {
        return MemberProfile::with('user')
            ->expiringWithin($days)
            ->get();
    }
}
