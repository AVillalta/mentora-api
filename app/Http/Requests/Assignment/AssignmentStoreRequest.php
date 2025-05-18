<?php

namespace App\Http\Requests\Assignment;

use Illuminate\Foundation\Http\FormRequest;

class AssignmentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'course_id' => 'required|uuid|exists:courses,id',
            'due_date' => 'required|date',
            'points' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser una cadena.',
            'title.max' => 'El título no puede tener más de 255 caracteres.',
            'description.required' => 'La descripción es obligatoria.',
            'description.string' => 'La descripción debe ser una cadena.',
            'course_id.required' => 'El curso es obligatorio.',
            'course_id.exists' => 'El curso seleccionado no existe.',
            'due_date.required' => 'La fecha límite es obligatoria.',
            'due_date.date' => 'La fecha límite debe ser una fecha válida.',
            'points.required' => 'Los puntos son obligatorios.',
            'points.integer' => 'Los puntos deben ser un número entero.',
            'points.min' => 'Los puntos no pueden ser negativos.',
        ];
    }
}