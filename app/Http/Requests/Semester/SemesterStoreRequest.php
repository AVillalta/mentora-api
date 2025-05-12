<?php

namespace App\Http\Requests\Semester;

use App\Data\Semester\SemesterCalendarData;
use Illuminate\Foundation\Http\FormRequest;

class SemesterStoreRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:semesters'],
            'start_date' => ['required', 'date', 'before:end_date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'calendar' => ['nullable', 'array'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => 'semester name',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'calendar' => 'calendar'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'The :attribute field is required.',
            'name.unique' => 'A semester with this :attribute already exists.',
            'start_date.required' => 'The :attribute field is required.',
            'start_date.before' => 'The :attribute must be before the end date.',
            'end_date.required' => 'The :attribute field is required.',
            'end_date.after' => 'The :attribute must be after the start date.',
            'calendar.json' => 'The :attribute must be valid JSON.',
        ];
    }

    /**
     * Transform the validated data into a structured format using DTO
     *
     * @return array
     */
    public function validatedData() : array 
    {
        $validated = $this->validated();
        $validated['calendar'] = isset($validated['calendar'])
            ? SemesterCalendarData::fromArray($validated['calendar'], true)
            : new SemesterCalendarData();

        return $validated;
    }
}
