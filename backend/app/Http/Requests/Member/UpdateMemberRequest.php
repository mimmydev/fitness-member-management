<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ActiveStatusRequiresValidEndDate;

/**
 * UpdateMemberRequest
 *
 * Validates data for updating an existing member profile.
 * Similar to StoreMemberRequest but all fields are optional.
 */
class UpdateMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Note: Detailed authorization (ownership check) is handled by
     * MemberProfilePolicy in the controller. This only checks authentication.
     */
    public function authorize(): bool
    {
        // Only authenticated users can update member profiles
        // Ownership verification is handled by MemberProfilePolicy
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'last_name' => ['sometimes', 'required', 'string', 'max:100'],
            // Phone: Malaysian format only (10-11 digits starting with 0)
            'phone' => ['nullable', 'string', 'regex:/^0\d{9,10}$/'],
            // Date of birth: must be 18 years or older
            'date_of_birth' => ['nullable', 'date', 'before:today', 'before_or_equal:' . now()->subYears(18)->format('Y-m-d')],
            'gender' => ['nullable', 'in:male,female,other,prefer_not_to_say'],
            // Address: limited to 500 characters to prevent data overflow
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'membership_start_date' => ['nullable', 'date'],
            'membership_end_date' => ['nullable', 'date', 'after:membership_start_date'],
            'membership_status' => ['nullable', 'in:active,inactive,suspended,expired', new ActiveStatusRequiresValidEndDate($this->input('membership_end_date'))],
            'membership_type' => ['nullable', 'in:basic,premium,vip'],
            'emergency_contact_name' => ['nullable', 'string', 'max:100'],
            // Emergency phone: Malaysian format only (10-11 digits starting with 0)
            'emergency_contact_phone' => ['nullable', 'string', 'regex:/^0\d{9,10}$/'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'date_of_birth.before_or_equal' => 'Member must be at least 18 years old.',
            'membership_end_date.after' => 'Membership end date must be after start date.',
            'phone.regex' => 'Phone must be Malaysian format: 10-11 digits starting with 0 (e.g., 0123456789).',
            'emergency_contact_phone.regex' => 'Emergency contact phone must be Malaysian format: 10-11 digits starting with 0 (e.g., 0123456789).',
            'address.max' => 'Address cannot exceed 500 characters.',
        ];
    }
}
