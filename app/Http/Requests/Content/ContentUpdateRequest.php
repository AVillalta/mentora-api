<?php

namespace App\Http\Requests\Content;

use Illuminate\Foundation\Http\FormRequest;

class ContentUpdateRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'bibliography' => 'sometimes|string',
            'order' => 'sometimes|integer',
            'course_id' => 'sometimes|uuid|exists:courses,id', 
            'grade_id' => 'sometimes|uuid|exists:grades,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.sometimes' => 'The :attribute field is optional, but if provided, it must be a string.',
            'name.string' => 'The :attribute must be a string.',
            'name.max' => 'The :attribute may not be greater than 255 characters.',

            'description.sometimes' => 'The :attribute field is optional, but if provided, it must be a string.',
            'description.string' => 'The :attribute must be a string.',

            'bibliography.sometimes' => 'The :attribute field is optional, but if provided, it must be a string.',
            'bibliography.string' => 'The :attribute must be a string.',

            'order.sometimes' => 'The :attribute field is optional, but if provided, it must be a string.',
            'order.integer' => 'The :attribute must be a integer.',

            'course_id.exists' => 'The selected :attribute does not exist.',
            
            'grade_id.exists' => 'The selected :attribute does not exist.',
        ];
    }
}
