<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreMemberRequest
 *
 * Validates data for creating a new member profile.
 * Encapsulates validation logic for member creation.
 */
class StoreMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only authenticated users can create member profiles
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
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other,prefer_not_to_say'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'membership_start_date' => ['nullable', 'date'],
            'membership_end_date' => ['nullable', 'date', 'after:membership_start_date'],
            'membership_status' => ['nullable', 'in:active,inactive,suspended,expired'],
            'membership_type' => ['nullable', 'in:basic,premium,vip'],
            'emergency_contact_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'membership_end_date.after' => 'Membership end date must be after start date.',
        ];
    }
}
