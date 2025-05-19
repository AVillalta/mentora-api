<?php

namespace App\Http\Requests\Semester;

use App\Data\Semester\SemesterCalendarData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class SemesterStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:semesters'],
            'start_date' => ['required', 'date', 'before:end_date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'calendar' => ['nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->input('is_active') && DB::table('semesters')->where('is_active', true)->exists()) {
                $validator->errors()->add('is_active', 'Ya existe un semestre activo.');
            }
        });
    }

    public function attributes()
    {
        return [
            'name' => 'semester name',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'calendar' => 'calendar',
            'is_active' => 'active status',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The :attribute field is required.',
            'name.unique' => 'A semester with this :attribute already exists.',
            'start_date.required' => 'The :attribute field is required.',
            'start_date.before' => 'The :attribute must be before the end date.',
            'end_date.required' => 'The :attribute field is required.',
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