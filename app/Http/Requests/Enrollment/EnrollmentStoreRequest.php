<?php

namespace App\Http\Requests\Enrollment;

use App\Rules\RoleValidation;
use Illuminate\Foundation\Http\FormRequest;

class EnrollmentStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasAnyRole(['admin']) && auth()->user()->hasPermissionTo('create-enrollments');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'course_id' => ['required', 'uuid', 'exists:courses,id'],
            'student_id' => ['required', 'uuid', 'exists:users,id', new RoleValidation('student')],
            'enrollment_date' => ['required', 'date', 'before_or_equal:today']
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'course_id.required' => 'El ID del curso es obligatorio.',
            'course_id.uuid' => 'El ID del curso debe ser un UUID válido.',
            'course_id.exists' => 'El curso seleccionado no existe.',
            'student_id.required' => 'El ID del estudiante es obligatorio.',
            'student_id.uuid' => 'El ID del estudiante debe ser un UUID válido.',
            'student_id.exists' => 'El estudiante seleccionado no existe.',
            'enrollment_date.required' => 'La fecha de matrícula es obligatoria.',
            'enrollment_date.date' => 'La fecha de matrícula debe ser una fecha válida.',
            'enrollment_date.before_or_equal' => 'La fecha de matrícula no puede ser futura.',
        ];
    }
}