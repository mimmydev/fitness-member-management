<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * MemberResource
 *
 * Transforms MemberProfile model data for API responses.
 * Provides consistent data structure for frontend consumption.
 */
class MemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            // Personal information
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name, // Accessor from model
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth?->toDateString(),
            'gender' => $this->gender,

            // Address information
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,

            // Membership information
            'membership_start_date' => $this->membership_start_date->toDateString(),
            'membership_end_date' => $this->membership_end_date?->toDateString(),
            'membership_status' => $this->membership_status,
            'membership_type' => $this->membership_type,
            'is_expired' => $this->isExpired(), // Method from model

            // Emergency contact
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,

            // Timestamps
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),

            // Include user data if loaded (for member listings)
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
