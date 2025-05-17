<?php

namespace App\Http\Requests\Content;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'bibliography' => 'sometimes|string',
            'order' => 'sometimes|integer',
            'file' => 'sometimes|file|mimes:pdf,doc,docx,pptx,mp4|max:102400',
            'type' => ['sometimes', Rule::in(['document', 'presentation', 'video', 'code', 'spreadsheet'])],
            'format' => 'sometimes|string|in:pdf,doc,docx,pptx,mp4',
            'duration' => 'sometimes|string',
            'course_id' => 'sometimes|uuid|exists:courses,id',
            'grade_id' => 'sometimes|nullable|uuid|exists:grades,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'El nombre debe ser una cadena.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'description.string' => 'La descripción debe ser una cadena.',
            'bibliography.string' => 'La bibliografía debe ser una cadena.',
            'order.integer' => 'El orden debe ser un número entero.',
            'file.mimes' => 'El archivo debe ser de tipo PDF, DOC, DOCX, PPTX o MP4.',
            'file.max' => 'El archivo no puede exceder 100MB.',
            'type.in' => 'El tipo de material no es válido.',
            'format.in' => 'El formato no es válido.',
            'course_id.exists' => 'El curso seleccionado no existe.',
            'grade_id.exists' => 'La nota seleccionada no existe.',
        ];
    }
}