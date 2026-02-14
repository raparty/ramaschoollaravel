<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Attendance;

/**
 * Mark Attendance Request
 * 
 * Validates bulk attendance marking
 */
class MarkAttendanceRequest extends FormRequest
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
            'date' => ['required', 'date', 'before_or_equal:today'],
            'class_id' => ['required', 'exists:classes,id'],
            'attendance' => ['required', 'array', 'min:1'],
            'attendance.*.admission_id' => ['required', 'exists:admissions,id'],
            'attendance.*.status' => [
                'required',
                'string',
                'in:' . implode(',', [
                    Attendance::STATUS_PRESENT,
                    Attendance::STATUS_ABSENT,
                    Attendance::STATUS_LATE,
                    Attendance::STATUS_HALF_DAY,
                    Attendance::STATUS_LEAVE,
                ]),
            ],
            'attendance.*.in_time' => ['nullable', 'date_format:H:i'],
            'attendance.*.out_time' => ['nullable', 'date_format:H:i', 'after:attendance.*.in_time'],
            'attendance.*.remarks' => ['nullable', 'string', 'max:500'],
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
            'date.required' => 'Please select a date.',
            'date.date' => 'Invalid date format.',
            'date.before_or_equal' => 'Cannot mark attendance for future dates.',
            'class_id.required' => 'Please select a class.',
            'class_id.exists' => 'Selected class does not exist.',
            'attendance.required' => 'Please mark attendance for at least one student.',
            'attendance.array' => 'Invalid attendance data format.',
            'attendance.min' => 'Please mark attendance for at least one student.',
            'attendance.*.admission_id.required' => 'Student ID is required.',
            'attendance.*.admission_id.exists' => 'Student does not exist.',
            'attendance.*.status.required' => 'Attendance status is required.',
            'attendance.*.status.in' => 'Invalid attendance status.',
            'attendance.*.in_time.date_format' => 'Invalid in time format. Use HH:MM.',
            'attendance.*.out_time.date_format' => 'Invalid out time format. Use HH:MM.',
            'attendance.*.out_time.after' => 'Out time must be after in time.',
            'attendance.*.remarks.max' => 'Remarks cannot exceed 500 characters.',
        ];
    }
}
