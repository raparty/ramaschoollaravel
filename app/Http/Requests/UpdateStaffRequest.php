<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Update Staff Request
 * 
 * Validates staff update form data
 */
class UpdateStaffRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $staffId = $this->route('staff');
        
        return [
            'staff_id' => ['required', 'string', 'max:50', Rule::unique('staff', 'staff_id')->ignore($staffId)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('staff', 'email')->ignore($staffId)],
            'phone' => ['nullable', 'string', 'max:20'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'department_id' => ['required', 'exists:departments,id'],
            'position_id' => ['required', 'exists:positions,id'],
            'qualification' => ['nullable', 'string', 'max:500'],
            'address' => ['nullable', 'string', 'max:500'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'joining_date' => ['required', 'date'],
            'basic_salary' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'staff_id' => 'staff ID',
            'name' => 'staff name',
            'email' => 'email address',
            'phone' => 'phone number',
            'photo' => 'photo',
            'department_id' => 'department',
            'position_id' => 'position',
            'qualification' => 'qualification',
            'address' => 'address',
            'date_of_birth' => 'date of birth',
            'joining_date' => 'joining date',
            'basic_salary' => 'basic salary',
            'status' => 'status',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'staff_id.required' => 'Staff ID is required.',
            'staff_id.unique' => 'This staff ID already exists.',
            'email.unique' => 'This email address is already registered.',
            'department_id.exists' => 'Selected department does not exist.',
            'position_id.exists' => 'Selected position does not exist.',
            'photo.image' => 'Photo must be an image file.',
            'photo.mimes' => 'Photo must be in jpeg, png, or jpg format.',
            'photo.max' => 'Photo size must not exceed 2MB.',
        ];
    }
}
