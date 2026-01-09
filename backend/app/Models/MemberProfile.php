<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * MemberProfile Model
 *
 * Contains business-specific member data.
 * Separated from User model to follow Single Responsibility Principle.
 * Soft deletes enabled for audit trail and data recovery.
 */
class MemberProfile extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'state',
        'postal_code',
        'membership_start_date',
        'membership_end_date',
        'membership_status',
        'membership_type',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'membership_start_date' => 'date',
            'membership_end_date' => 'date',
        ];
    }

    /**
     * Get the user that owns the member profile.
     *
     * Inverse of User->memberProfile() relationship.
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include active members.
     */
    public function scopeActive($query)
    {
        return $query->where('membership_status', 'active');
    }

    /**
     * Scope a query to only include members with expiring memberships.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $days Number of days to check
     */
    public function scopeExpiringWithin($query, int $days = 30)
    {
        return $query->where('membership_status', 'active')
            ->whereNotNull('membership_end_date')
            ->whereBetween('membership_end_date', [now(), now()->addDays($days)]);
    }

    /**
     * Get the member's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Check if membership is expired.
     */
    public function isExpired(): bool
    {
        return $this->membership_end_date &&
               $this->membership_end_date->isPast() &&
               $this->membership_status !== 'expired';
    }
}
