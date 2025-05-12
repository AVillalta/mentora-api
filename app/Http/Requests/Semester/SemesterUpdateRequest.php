<?php

namespace App\Http\Requests\Semester;

use App\Data\Semester\SemesterCalendarData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

class SemesterUpdateRequest extends FormRequest
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
        //$semester = $this->route('semester');
        //if (is_numeric($semester)) {
        //$semester = \App\Models\Semester\Semester::find($semester);
        //}
        //$startDate = $semester ? $semester->start_date : null;
        //$endDate = $semester ? $semester->end_date : null;

        return [
            'name' => ['sometimes','string', 'max:255', Rule::unique('semesters')->ignore($this->semester)],
            'start_date' => ['sometimes', 'date', 'before:end_date'],// . ($endDate ?? 'end_date')],
            'end_date' => ['sometimes', 'date', 'after:start_date'], //. ($startDate ?? 'start_date')],
            'calendar' => ['nullable', 'array']
        ];
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

            'calendar.sometimes' => 'The :attribute field is optional, but if provided, it must be an array.',
            'calendar.array' => 'The :attribute must be an array.',
        ];
    }


    /**
     * Transform the validated data into a structured format using DTO
     *
     * @return array
     */
    public function validatedData(): array
    {
        $validated = $this->validated();
        $validated['calendar'] = isset($validated['calendar'])
            ? SemesterCalendarData::fromArray($validated['calendar'], true)
            : new SemesterCalendarData();

        return $validated;
    }
}
