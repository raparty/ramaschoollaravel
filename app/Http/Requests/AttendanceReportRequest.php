<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Attendance Report Request
 * 
 * Validates attendance report generation parameters
 */
class AttendanceReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Add authorization logic as needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'report_type' => ['required', 'string', 'in:student,class,monthly,daterange'],
            'start_date' => ['required_if:report_type,daterange', 'date'],
            'end_date' => ['required_if:report_type,daterange', 'date', 'after_or_equal:start_date'],
            'month' => ['required_if:report_type,monthly', 'integer', 'between:1,12'],
            'year' => ['required_if:report_type,monthly', 'integer', 'min:2000', 'max:2100'],
            'class_id' => ['required_if:report_type,class', 'exists:classes,id'],
            'admission_id' => ['required_if:report_type,student', 'exists:admissions,id'],
            'export' => ['nullable', 'string', 'in:csv,pdf'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'report_type.required' => 'Please select a report type.',
            'report_type.in' => 'Invalid report type selected.',
            'start_date.required_if' => 'Start date is required for date range reports.',
            'start_date.date' => 'Invalid start date format.',
            'end_date.required_if' => 'End date is required for date range reports.',
            'end_date.date' => 'Invalid end date format.',
            'end_date.after_or_equal' => 'End date must be on or after start date.',
            'month.required_if' => 'Month is required for monthly reports.',
            'month.between' => 'Month must be between 1 and 12.',
            'year.required_if' => 'Year is required for monthly reports.',
            'year.min' => 'Year must be at least 2000.',
            'year.max' => 'Year cannot be after 2100.',
            'class_id.required_if' => 'Class is required for class reports.',
            'class_id.exists' => 'Selected class does not exist.',
            'admission_id.required_if' => 'Student is required for student reports.',
            'admission_id.exists' => 'Selected student does not exist.',
            'export.in' => 'Invalid export format. Choose CSV or PDF.',
        ];
    }
}
