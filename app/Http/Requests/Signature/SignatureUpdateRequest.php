<?php

namespace App\Http\Requests\Signature;

use App\Rules\RoleValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SignatureUpdateRequest extends FormRequest
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
            'syllabus' => ['sometimes', 'array'],
            'syllabus_pdf' => ['sometimes', 'file', 'mimes:pdf', 'max:10240'],
            'professor_id' => ['sometimes', 'uuid', 'exists:users,id', new RoleValidation('professor')],
        ];
    }

    public function messages(): array
    {
        return [
            'name.sometimes' => ':attribute is optional, but if provided, it must be a string.',
            'name.string' => ':attribute must be a string.',
            'name.max' => ':attribute may not be greater than :max characters.',
            
            'syllabus.sometimes' => ':attribute is optional, but if provided, it must be a array.',
            'syllabus.array' => ':attribute must be a valid array.',

            'syllabus_pdf.sometimes' => ':attribute is optional, but if provided, it must be a valid file.',
            'syllabus_pdf.file' => ':attribute must be a valid file.',
            'syllabus_pdf.mimes' => ':attribute must be a PDF file.',
            'syllabus_pdf.max' => ':attribute may not be greater than :max kilobytes.',
            
            'professor_id.sometimes' => ':attribute is optional, but if provided, it must be a valid user ID with a professor role.',
            'professor_id.exists' => ':attribute does not exist in the database.',
            'professor_id.professor' => ':attribute must be assigned a professor role.',
        ];
    }

}
