<?php

namespace App\Http\Requests\Content;

use Illuminate\Foundation\Http\FormRequest;

class ContentStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'bibliography' => 'nullable|string',
            'order' => 'required|integer',
            'course_id' => 'required|uuid|exists:courses,id', 
            'grade_id' => 'required|uuid|exists:grades,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => ':attribute is required.',
            'name.string' => ':attribute must be a string.',
            'name.max' => ':attribute may not be greater than :max characters.',

            'description.required' => ':attribute is required.',
            'description.string' => ':attribute must be a string.',

            'bibliography.required' => ':attribute is required.',
            'bibliography.string' => ':attribute must be a string.',

            'order.required' => 'The :attribute field is required.',
            'order.integer' => ':attribute must be a integer.',

            'course_id.required' => 'The :attribute field is required.',
            'course_id.exists' => 'The selected :attribute does not exist.',

            'grade_id.required' => 'The :attribute field is required.',
            'grade_id.exists' => 'The selected :attribute does not exist.',
            
        ];
    }
}
