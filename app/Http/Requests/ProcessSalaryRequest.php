<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Process Salary Request
 * 
 * Validates salary processing form data
 */
class ProcessSalaryRequest extends FormRequest
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
        return [
            'staff_id' => ['required', 'exists:staff,id'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2000', 'max:' . (date('Y') + 1)],
            'basic_salary' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'allowances' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'deductions' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'payment_method' => ['nullable', 'string', 'in:cash,bank_transfer,cheque'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Set default values for allowances and deductions if not provided
        $this->merge([
            'allowances' => $this->allowances ?? 0,
            'deductions' => $this->deductions ?? 0,
        ]);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'staff_id' => 'staff member',
            'month' => 'month',
            'year' => 'year',
            'basic_salary' => 'basic salary',
            'allowances' => 'allowances',
            'deductions' => 'deductions',
            'payment_method' => 'payment method',
            'notes' => 'notes',
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
            'staff_id.required' => 'Please select a staff member.',
            'staff_id.exists' => 'Selected staff member does not exist.',
            'month.required' => 'Month is required.',
            'month.min' => 'Month must be between 1 and 12.',
            'month.max' => 'Month must be between 1 and 12.',
            'year.required' => 'Year is required.',
            'basic_salary.required' => 'Basic salary is required.',
            'basic_salary.min' => 'Basic salary must be greater than or equal to 0.',
        ];
    }
}
