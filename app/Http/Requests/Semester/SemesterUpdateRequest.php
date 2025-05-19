<?php

namespace App\Http\Requests\Semester;

use App\Data\Semester\SemesterCalendarData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class SemesterUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('semesters')->ignore($this->semester)],
            'start_date' => ['sometimes', 'date', 'before:end_date'],
            'end_date' => ['sometimes', 'date', 'after:start_date'],
            'calendar' => ['nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->input('is_active') && DB::table('semesters')->where('is_active', true)->where('id', '!=', $this->semester)->exists()) {
                $validator->errors()->add('is_active', 'Ya existe otro semestre activo.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.sometimes' => 'The :attribute field is optional, but if provided, it must be a string.',
            'name.string' => 'The :attribute must be a string.',
            'name.max' => 'The :attribute may not be greater than 255 characters.',
            'name.unique' => 'The :attribute has already been taken.',
            'start_date.sometimes' => 'The :attribute field is optional, but if provided, it must be a valid date.',
            'start_date.date' => 'The :attribute must be a valid date.',
            'start_date.before' => 'The :attribute must be before the end date.',
            'end_date.sometimes' => 'The :attribute field is optional, but if provided, it must be a valid date.',
            'end_date.date' => 'The :attribute must be a valid date.',
            'end_date.after' => 'The :attribute must be after the start date.',
            'calendar.array' => 'The :attribute must be an array.',
            'is_active.boolean' => 'The :attribute must be a boolean.',
        ];
    }

    public function validatedData(): array
    {
        $validated = $this->validated();
        $validated['calendar'] = isset($validated['calendar'])
            ? SemesterCalendarData::fromArray($validated['calendar'], true)
            : new SemesterCalendarData();
        return $validated;
    }
}