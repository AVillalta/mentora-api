<?php

namespace App\Http\Requests\Semester;

use App\Data\Semester\SemesterCalendarData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SemesterUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('semesters')->ignore($this->route('semester'))],
            'start_date' => ['sometimes', 'date', 'before:end_date'],
            'end_date' => ['sometimes', 'date', 'after:start_date'],
            'calendar' => ['nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (
                $this->input('is_active') &&
                DB::table('semesters')
                    ->where('is_active', true)
                    ->where('id', '!=', $this->route('semester'))
                    ->exists()
            ) {
                $validator->errors()->add('is_active', 'Ya existe un semestre activo.');
            }
        });
    }

    public function attributes()
    {
        return [
            'name' => 'nombre del semestre',
            'start_date' => 'fecha de inicio',
            'end_date' => 'fecha de fin',
            'calendar' => 'calendario',
            'is_active' => 'estado activo',
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'Ya existe un semestre con este :attribute.',
            'start_date.before' => 'La :attribute debe ser anterior a la fecha de fin.',
            'end_date.after' => 'La :attribute debe ser posterior a la fecha de inicio.',
            'calendar.array' => 'El :attribute debe ser un array.',
            'is_active.boolean' => 'El :attribute debe ser un valor booleano.',
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