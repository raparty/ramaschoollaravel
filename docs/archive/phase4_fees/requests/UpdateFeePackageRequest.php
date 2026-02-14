<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdateFeePackageRequest
 * 
 * Validates fee package update data
 */
class UpdateFeePackageRequest extends FormRequest
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
        $packageId = $this->route('fee_package')->id;

        return [
            'package_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('fees_package', 'package_name')->ignore($packageId),
            ],
            'total_amount' => ['required', 'numeric', 'min:0', 'max:999999.99'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'package_name' => 'package name',
            'total_amount' => 'total amount',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'package_name.required' => 'Package name is required.',
            'package_name.unique' => 'This package name already exists.',
            'total_amount.required' => 'Total amount is required.',
            'total_amount.min' => 'Total amount must be at least 0.',
        ];
    }
}
