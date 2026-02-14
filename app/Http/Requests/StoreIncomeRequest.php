<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncomeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // TODO: Add proper authorization
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => [
                'required',
                'exists:account_categories,id',
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],
            'date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'invoice_number' => [
                'nullable',
                'string',
                'max:100',
            ],
            'description' => [
                'required',
                'string',
                'max:1000',
            ],
            'payment_method' => [
                'required',
                'string',
                'in:cash,cheque,bank_transfer,online,card',
            ],
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
            'category_id.required' => 'Income category is required.',
            'category_id.exists' => 'Selected category does not exist.',
            'amount.required' => 'Amount is required.',
            'amount.min' => 'Amount must be greater than or equal to 0.',
            'date.required' => 'Date is required.',
            'date.before_or_equal' => 'Date cannot be in the future.',
            'description.required' => 'Description is required.',
            'payment_method.required' => 'Payment method is required.',
        ];
    }
}
