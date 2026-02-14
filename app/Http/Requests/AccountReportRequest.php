<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountReportRequest extends FormRequest
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
            'report_type' => [
                'required',
                'string',
                'in:summary,details,category_wise,monthly,yearly',
            ],
            'start_date' => [
                'nullable',
                'date',
            ],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
            'month' => [
                'nullable',
                'integer',
                'between:1,12',
            ],
            'year' => [
                'nullable',
                'integer',
                'between:2000,2100',
            ],
            'category_id' => [
                'nullable',
                'exists:account_categories,id',
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
            'report_type.required' => 'Report type is required.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'month.between' => 'Month must be between 1 and 12.',
            'year.between' => 'Year must be a valid year.',
        ];
    }
}
