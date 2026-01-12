import { z } from 'zod'

// Malaysian phone number regex: 10-11 digits starting with 0
const malaysianPhoneRegex = /^0\d{9,10}$/

export const memberFormSchema = z.object({
  first_name: z.string().min(2, 'First name must be at least 2 characters'),
  last_name: z.string().min(2, 'Last name must be at least 2 characters'),
  phone: z
    .string()
    .regex(malaysianPhoneRegex, 'Phone must be Malaysian format: 10-11 digits starting with 0 (e.g., 0123456789)')
    .optional()
    .or(z.literal('')),
  date_of_birth: z
    .string()
    .refine((val) => {
      if (!val) return true; // Optional field
      const birthDate = new Date(val);
      const today = new Date();
      const age = today.getFullYear() - birthDate.getFullYear();
      const monthDiff = today.getMonth() - birthDate.getMonth();
      const dayDiff = today.getDate() - birthDate.getDate();

      // Adjust age if birthday hasn't occurred this year
      const actualAge = monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)
        ? age - 1
        : age;

      return actualAge >= 18;
    }, 'Member must be at least 18 years old')
    .optional(),
  gender: z.enum(['male', 'female', 'other']).optional(),
  address: z
    .string()
    .max(500, 'Address cannot exceed 500 characters')
    .optional()
    .or(z.literal('')),
  city: z.string().optional(),
  state: z.string().optional(),
  postal_code: z.string().optional(),
  membership_start_date: z.string().min(1, 'Start date is required'),
  membership_end_date: z.string().optional(),
  membership_status: z.enum(['active', 'inactive', 'expired']),
  membership_type: z.enum(['basic', 'premium', 'vip']),
  emergency_contact_name: z.string().optional(),
  emergency_contact_phone: z
    .string()
    .regex(malaysianPhoneRegex, 'Emergency phone must be Malaysian format: 10-11 digits starting with 0 (e.g., 0123456789)')
    .optional()
    .or(z.literal('')),
}).refine((data) => {
  // Status/End Date Alignment: Cannot be active with past end date
  if (data.membership_status === 'active' && data.membership_end_date) {
    const endDate = new Date(data.membership_end_date);
    const today = new Date();
    today.setHours(0, 0, 0, 0); // Reset time to compare dates only

    // End date must be today or in the future
    return endDate >= today;
  }
  return true;
}, {
  message: 'Cannot set status as "active" when membership end date is in the past',
  path: ['membership_status'], // Error shows on status field
})

export type MemberFormData = z.infer<typeof memberFormSchema>
