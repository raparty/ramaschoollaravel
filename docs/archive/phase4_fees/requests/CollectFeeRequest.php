<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * CollectFeeRequest
 * 
 * Validates fee collection data
 * Replaces manual validation from add_student_fees.php
 */
class CollectFeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by middleware/gates
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'registration_no' => ['required', 'string', 'exists:admissions,reg_no'],
            'fees_term' => ['required', 'integer', 'exists:fees_term,id'],
            'fees_amount' => ['required', 'numeric', 'min:1', 'max:999999.99'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'registration_no' => 'registration number',
            'fees_term' => 'fee term',
            'fees_amount' => 'fee amount',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'registration_no.required' => 'Registration number is required.',
            'registration_no.exists' => 'Student not found with this registration number.',
            'fees_term.required' => 'Fee term is required.',
            'fees_term.exists' => 'Invalid fee term selected.',
            'fees_amount.required' => 'Fee amount is required.',
            'fees_amount.min' => 'Fee amount must be at least 1.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Custom validation: Check if amount doesn't exceed pending balance
            // This would require fetching student's pending amount
            // For now, we'll skip this validation and handle it in the controller
        });
    }
}
