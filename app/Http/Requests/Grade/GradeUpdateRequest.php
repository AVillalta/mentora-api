<?php

namespace App\Http\Requests\Grade;

use Illuminate\Foundation\Http\FormRequest;

class GradeUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'grade_type' => ['sometimes', 'string', 'in:ordinary,extraordinary,work,partial,final'],
            'grade_value' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:10'],
            'grade_date' => ['sometimes', 'date'],
            'enrollment_id' => ['sometimes', 'uuid', 'exists:enrollments,id'],
            'assignment_id' => ['sometimes', 'nullable', 'uuid', 'exists:assignments,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.string' => 'El título debe ser una cadena.',
            'title.max' => 'El título no puede tener más de 255 caracteres.',
            'grade_type.in' => 'The :attribute must be one of the following: ordinary, extraordinary, work, partial, final.',
            'grade_value.numeric' => 'The :attribute must be a valid number.',
            'grade_value.min' => 'The :attribute cannot be less than 0.',
            'grade_value.max' => 'The :attribute cannot exceed 10.',
            'grade_date.date' => 'The :attribute must be a valid date.',
            'enrollment_id.exists' => 'The provided enrollment ID does not exist.',
            'assignment_id.exists' => 'The provided assignment ID does not exist.',
        ];
    }
}