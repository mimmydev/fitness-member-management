<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import {
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form';
import { useForm } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import { memberFormSchema, type MemberFormData } from '@/lib/validations';
import type { MemberProfile } from '@/types/api';
import { Loader2, User, CreditCard, MapPin } from 'lucide-vue-next';

const props = defineProps<{
  mode: 'create' | 'edit';
  initialData?: MemberProfile;
  isSubmitting?: boolean;
}>();

const emit = defineEmits<{
  submit: [data: MemberFormData];
  cancel: [];
}>();

const form = useForm({
  validationSchema: toTypedSchema(memberFormSchema),
  initialValues: props.initialData
    ? {
        first_name: props.initialData.first_name,
        last_name: props.initialData.last_name,
        phone: props.initialData.phone || '',
        date_of_birth: props.initialData.date_of_birth || '',
        gender: (props.initialData.gender as any) || undefined,
        address: props.initialData.address || '',
        city: props.initialData.city || '',
        state: props.initialData.state || '',
        postal_code: props.initialData.postal_code || '',
        membership_start_date: props.initialData.membership_start_date,
        membership_end_date: props.initialData.membership_end_date || '',
        membership_status: props.initialData.membership_status,
        membership_type: props.initialData.membership_type,
        emergency_contact_name: props.initialData.emergency_contact_name || '',
        emergency_contact_phone:
          props.initialData.emergency_contact_phone || '',
      }
    : {
        first_name: '',
        last_name: '',
        phone: '',
        date_of_birth: '',
        gender: undefined,
        address: '',
        city: '',
        state: '',
        postal_code: '',
        membership_start_date: new Date().toISOString().split('T')[0],
        membership_end_date: '',
        membership_status: 'active' as const,
        membership_type: 'basic' as const,
        emergency_contact_name: '',
        emergency_contact_phone: '',
      },
});

const onSubmit = form.handleSubmit((values) => {
  emit('submit', values);
});

const inputClass =
  'bg-slate-50 border-slate-200 focus:bg-white focus:border-indigo-500 transition-colors';
// New class for the error message to keep it from breaking layout
const errorClass = 'absolute -bottom-5 left-0 text-[11px]';
</script>

<template>
  <form @submit="onSubmit" class="space-y-8 pb-4">
    <!-- Section 1: Personal Info -->
    <div class="space-y-5">
      <!-- Increased vertical space slightly -->
      <div class="flex items-center gap-2 mb-4">
        <div class="p-2 bg-indigo-50 rounded-full">
          <User class="h-4 w-4 text-indigo-600" />
        </div>
        <h3 class="text-base font-semibold text-slate-800">
          Personal Information
        </h3>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
        <!-- Increased gap-y-6 to allow space for error message -->
        <FormField v-slot="{ componentField }" name="first_name">
          <FormItem class="relative">
            <FormLabel class="text-slate-600">First Name</FormLabel>
            <FormControl>
              <Input
                placeholder="John"
                v-bind="componentField"
                :class="inputClass"
              />
            </FormControl>
            <FormMessage :class="errorClass" />
          </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="last_name">
          <FormItem class="relative">
            <FormLabel class="text-slate-600">Last Name</FormLabel>
            <FormControl>
              <Input
                placeholder="Doe"
                v-bind="componentField"
                :class="inputClass"
              />
            </FormControl>
            <FormMessage :class="errorClass" />
          </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="phone">
          <FormItem class="relative">
            <FormLabel class="text-slate-600">Phone Number</FormLabel>
            <FormControl>
              <Input
                placeholder="01123459012"
                v-bind="componentField"
                :class="inputClass"
              />
            </FormControl>
            <FormMessage :class="errorClass" />
          </FormItem>
        </FormField>

        <div class="grid grid-cols-2 gap-4">
          <FormField v-slot="{ componentField }" name="date_of_birth">
            <FormItem class="relative">
              <FormLabel class="text-slate-600">Date of Birth</FormLabel>
              <FormControl>
                <Input
                  type="date"
                  v-bind="componentField"
                  :class="inputClass"
                />
              </FormControl>
              <FormMessage :class="errorClass" />
            </FormItem>
          </FormField>

          <FormField v-slot="{ value, handleChange }" name="gender">
            <FormItem class="relative">
              <FormLabel class="text-slate-600">Gender</FormLabel>
              <Select :model-value="value" @update:model-value="handleChange">
                <FormControl>
                  <SelectTrigger :class="inputClass">
                    <SelectValue placeholder="Select" />
                  </SelectTrigger>
                </FormControl>
                <SelectContent>
                  <SelectItem value="male">Male</SelectItem>
                  <SelectItem value="female">Female</SelectItem>
                  <SelectItem value="other">Other</SelectItem>
                </SelectContent>
              </Select>
              <FormMessage :class="errorClass" />
            </FormItem>
          </FormField>
        </div>
      </div>
    </div>

    <!-- Section 2: Membership -->
    <div class="space-y-5">
      <div class="flex items-center gap-2 mb-4 mt-2">
        <div class="p-2 bg-indigo-50 rounded-full">
          <CreditCard class="h-4 w-4 text-indigo-600" />
        </div>
        <h3 class="text-base font-semibold text-slate-800">
          Membership Details
        </h3>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
        <FormField v-slot="{ value, handleChange }" name="membership_type">
          <FormItem class="relative">
            <FormLabel class="text-slate-600">Plan Type</FormLabel>
            <Select :model-value="value" @update:model-value="handleChange">
              <FormControl>
                <SelectTrigger :class="inputClass">
                  <SelectValue placeholder="Select plan" />
                </SelectTrigger>
              </FormControl>
              <SelectContent>
                <SelectItem value="basic">Basic Plan</SelectItem>
                <SelectItem value="premium">Premium Plan</SelectItem>
                <SelectItem value="vip">VIP Access</SelectItem>
              </SelectContent>
            </Select>
            <FormMessage :class="errorClass" />
          </FormItem>
        </FormField>

        <FormField v-slot="{ value, handleChange }" name="membership_status">
          <FormItem class="relative">
            <FormLabel class="text-slate-600">Status</FormLabel>
            <Select :model-value="value" @update:model-value="handleChange">
              <FormControl>
                <SelectTrigger :class="inputClass">
                  <SelectValue placeholder="Select status" />
                </SelectTrigger>
              </FormControl>
              <SelectContent>
                <SelectItem value="active">Active</SelectItem>
                <SelectItem value="inactive">Inactive</SelectItem>
                <SelectItem value="expired">Expired</SelectItem>
              </SelectContent>
            </Select>
            <FormMessage :class="errorClass" />
          </FormItem>
        </FormField>

        <div class="grid grid-cols-2 gap-4">
          <FormField v-slot="{ componentField }" name="membership_start_date">
            <FormItem class="relative">
              <FormLabel class="text-slate-600">Start Date</FormLabel>
              <FormControl>
                <Input
                  type="date"
                  v-bind="componentField"
                  :class="inputClass"
                />
              </FormControl>
              <FormMessage :class="errorClass" />
            </FormItem>
          </FormField>

          <FormField v-slot="{ componentField }" name="membership_end_date">
            <FormItem class="relative">
              <FormLabel class="text-slate-600">End Date</FormLabel>
              <FormControl>
                <Input
                  type="date"
                  v-bind="componentField"
                  :class="inputClass"
                />
              </FormControl>
              <FormMessage :class="errorClass" />
            </FormItem>
          </FormField>
        </div>

        <FormField v-slot="{ componentField }" name="emergency_contact_name">
          <FormItem class="relative">
            <FormLabel class="text-slate-600">Emergency Contact</FormLabel>
            <FormControl>
              <Input
                placeholder="Contact Name"
                v-bind="componentField"
                :class="inputClass"
              />
            </FormControl>
            <FormMessage :class="errorClass" />
          </FormItem>
        </FormField>
      </div>
    </div>

    <!-- Section 3: Address -->
    <div class="space-y-5">
      <div class="flex items-center gap-2 mb-4 mt-2">
        <div class="p-2 bg-indigo-50 rounded-full">
          <MapPin class="h-4 w-4 text-indigo-600" />
        </div>
        <h3 class="text-base font-semibold text-slate-800">Address</h3>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
        <div class="col-span-1 md:col-span-2">
          <FormField v-slot="{ componentField }" name="address">
            <FormItem class="relative">
              <FormLabel class="text-slate-600">Street Address</FormLabel>
              <FormControl>
                <Input
                  placeholder="123 Main St"
                  v-bind="componentField"
                  :class="inputClass"
                />
              </FormControl>
              <FormMessage :class="errorClass" />
            </FormItem>
          </FormField>
        </div>

        <FormField v-slot="{ componentField }" name="city">
          <FormItem class="relative">
            <FormLabel class="text-slate-600">City</FormLabel>
            <FormControl>
              <Input
                placeholder="City"
                v-bind="componentField"
                :class="inputClass"
              />
            </FormControl>
            <FormMessage :class="errorClass" />
          </FormItem>
        </FormField>

        <div class="grid grid-cols-2 gap-4">
          <FormField v-slot="{ componentField }" name="state">
            <FormItem class="relative">
              <FormLabel class="text-slate-600">State</FormLabel>
              <FormControl>
                <Input
                  placeholder="State"
                  v-bind="componentField"
                  :class="inputClass"
                />
              </FormControl>
              <FormMessage :class="errorClass" />
            </FormItem>
          </FormField>

          <FormField v-slot="{ componentField }" name="postal_code">
            <FormItem class="relative">
              <FormLabel class="text-slate-600">Zip Code</FormLabel>
              <FormControl>
                <Input
                  placeholder="12345"
                  v-bind="componentField"
                  :class="inputClass"
                />
              </FormControl>
              <FormMessage :class="errorClass" />
            </FormItem>
          </FormField>
        </div>
      </div>
    </div>

    <div class="flex justify-end gap-3 pt-6">
      <Button variant="ghost" type="button" @click="$emit('cancel')">
        Cancel
      </Button>
      <Button
        type="submit"
        :disabled="isSubmitting"
        class="bg-indigo-600 hover:bg-indigo-700"
      >
        <Loader2 v-if="isSubmitting" class="mr-2 h-4 w-4 animate-spin" />
        {{ mode === 'edit' ? 'Save Changes' : 'Create Member' }}
      </Button>
    </div>
  </form>
</template>
