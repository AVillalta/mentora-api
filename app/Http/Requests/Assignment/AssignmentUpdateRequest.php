<?php

namespace App\Http\Requests\Assignment;

use Illuminate\Foundation\Http\FormRequest;

class AssignmentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'course_id' => 'sometimes|uuid|exists:courses,id',
            'due_date' => 'sometimes|date',
            'points' => 'sometimes|integer|min:0',
            'submissions' => 'sometimes|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'title.string' => 'El título debe ser una cadena.',
            'title.max' => 'El título no puede tener más de 255 caracteres.',
            'description.string' => 'La descripción debe ser una cadena.',
            'course_id.exists' => 'El curso seleccionado no existe.',
            'due_date.date' => 'La fecha límite debe ser una fecha válida.',
            'points.integer' => 'Los puntos deben ser un número entero.',
            'points.min' => 'Los puntos no pueden ser negativos.',
            'submissions.integer' => 'El número de entregas debe ser un número entero.',
            'submissions.min' => 'El número de entregas no puede ser negativo.',
        ];
    }
}