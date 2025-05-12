<?php

namespace App\Http\Requests\Grade;

use Illuminate\Foundation\Http\FormRequest;

class GradeUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'grade_type' => ['sometimes', 'string', 'in:ordinary,extraordinary,work,partial,final'],
            'grade_value' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:10'],
            'grade_date' => ['sometimes', 'date'],
            'enrollment_id' => ['sometimes', 'uuid', 'exists:enrollments,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'grade_type.in' => 'The :attribute must be one of the following: ordinary, extraordinary, work, partial, final.',
            'grade_value.numeric' => 'The :attribute must be a valid number.',
            'grade_value.min' => 'The :attribute cannot be less than 0.',
            'grade_value.max' => 'The :attribute cannot exceed 10.',
            'grade_date.date' => 'The g:attribute must be a valid date.',
            'enrollment_id.exists' => 'The provided enrollment ID does not exist.',
        ];
    }
}
