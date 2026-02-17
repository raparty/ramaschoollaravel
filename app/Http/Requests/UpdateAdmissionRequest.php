<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdateAdmissionRequest
 * 
 * Validates student admission update data
 * Replaces manual validation from process_edit_admission.php
 */
class UpdateAdmissionRequest extends FormRequest
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
        $admissionId = $this->route('admission')->id;

        return [
            'student_name' => ['required', 'string', 'max:100'],
            'dob' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:Male,Female,Other'],
            'blood_group' => ['nullable', 'string', 'max:5'],
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'admission_date' => ['required', 'date'],
            'aadhaar_no' => [
                'required',
                'digits:12',
                Rule::unique('admissions', 'aadhaar_no')->ignore($admissionId),
            ],
            'guardian_name' => ['required', 'string', 'max:100'],
            'guardian_phone' => ['required', 'digits:10'],
            'emergency_contact' => ['nullable', 'digits:10'],
            'address' => ['nullable', 'string', 'max:500'],
            'health_issues' => ['nullable', 'string', 'max:1000'],
            'past_school_info' => ['nullable', 'string', 'max:1000'],
            'student_pic' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'], // Optional on update
            'student_pic_data' => ['nullable', 'string'], // For camera capture (base64 data)
            'aadhaar_doc' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'student_name' => 'student name',
            'dob' => 'date of birth',
            'class_id' => 'class',
            'aadhaar_no' => 'Aadhaar number',
            'guardian_name' => 'guardian name',
            'guardian_phone' => 'guardian phone',
            'emergency_contact' => 'emergency contact',
            'address' => 'address',
            'health_issues' => 'health issues',
            'past_school_info' => 'past school information',
            'student_pic' => 'student photo',
            'aadhaar_doc' => 'Aadhaar document',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'student_name.required' => 'Student name is required.',
            'dob.before' => 'Date of birth must be in the past.',
            'aadhaar_no.digits' => 'Aadhaar number must be exactly 12 digits.',
            'aadhaar_no.unique' => 'This Aadhaar number is already registered to another student.',
            'guardian_phone.digits' => 'Guardian phone must be exactly 10 digits.',
            'emergency_contact.digits' => 'Emergency contact must be exactly 10 digits.',
            'student_pic.max' => 'Student photo must not exceed 2MB.',
            'aadhaar_doc.max' => 'Aadhaar document must not exceed 5MB.',
        ];
    }
}
