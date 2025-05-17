<?php

namespace App\Http\Requests\Content;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'bibliography' => 'nullable|string',
            'order' => 'required|integer',
            'file' => 'required|file|mimes:pdf,doc,docx,pptx,mp4|max:102400',
            'type' => ['required', Rule::in(['document', 'presentation', 'video', 'code', 'spreadsheet'])],
            'format' => 'required|string|in:pdf,doc,docx,pptx,mp4',
            'duration' => 'nullable|string',
            'course_id' => 'required|uuid|exists:courses,id',
            'grade_id' => 'nullable|uuid|exists:grades,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'description.required' => 'La descripción es obligatoria.',
            'description.string' => 'La descripción debe ser una cadena.',
            'bibliography.string' => 'La bibliografía debe ser una cadena.',
            'order.required' => 'El orden es obligatorio.',
            'order.integer' => 'El orden debe ser un número entero.',
            'file.required' => 'El archivo es obligatorio.',
            'file.mimes' => 'El archivo debe ser de tipo PDF, DOC, DOCX, PPTX o MP4.',
            'file.max' => 'El archivo哈尔可 exceder 100MB.',
            'type.required' => 'El tipo de material es obligatorio.',
            'type.in' => 'El tipo de material no es válido.',
            'format.required' => 'El formato es obligatorio.',
            'format.in' => 'El formato no es válido.',
            'course_id.required' => 'El curso es obligatorio.',
            'course_id.exists' => 'El curso seleccionado no existe.',
            'grade_id.exists' => 'La nota seleccionada no existe.',
        ];
    }
}