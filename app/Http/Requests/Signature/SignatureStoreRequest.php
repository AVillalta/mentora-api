<?php

namespace App\Http\Requests\Signature;

use App\Rules\RoleValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SignatureStoreRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'syllabus' => ['nullable', 'array'],
            'syllabus_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'professor_id' => ['nullable', 'uuid', 'exists:users,id', new RoleValidation('professor')],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => ':attribute is required.',
            'name.string' => ':attribute must be a string.',
            'name.max' => ':attribute may not be greater than :max characters.',
            
            'syllabus.array' => ':attribute must be a valid array.',
            
            'syllabus_pdf.file' => ':attribute must be a valid file.',
            'syllabus_pdf.mimes' => ':attribute must be a PDF file.',
            'syllabus_pdf.max' => ':attribute may not be greater than :max kilobytes.',
            
            'professor_id.exists' => ':attribute does not exist in the database.',
            'professor_id.professor' => ':attribute must be assigned a professor role.',
        ];
    }
}
