<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Store Mark Request
 * 
 * Validates mark entry data
 */
class StoreMarkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Add authorization logic if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', 'exists:admissions,id'],
            'exam_subject_id' => ['required', 'integer', 'exists:exam_subjects,id'],
            'theory_marks' => ['required_without:is_absent', 'nullable', 'numeric', 'min:0'],
            'practical_marks' => ['nullable', 'numeric', 'min:0'],
            'is_absent' => ['nullable', 'boolean'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'student_id.required' => 'Student is required.',
            'student_id.exists' => 'Selected student does not exist.',
            'exam_subject_id.required' => 'Exam subject is required.',
            'exam_subject_id.exists' => 'Selected exam subject does not exist.',
            'theory_marks.required_without' => 'Theory marks are required when student is present.',
            'theory_marks.numeric' => 'Theory marks must be a number.',
            'theory_marks.min' => 'Theory marks cannot be negative.',
            'practical_marks.numeric' => 'Practical marks must be a number.',
            'practical_marks.min' => 'Practical marks cannot be negative.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set marks to 0 if student is absent
        if ($this->boolean('is_absent')) {
            $this->merge([
                'theory_marks' => 0,
                'practical_marks' => 0,
            ]);
        }
    }
}
