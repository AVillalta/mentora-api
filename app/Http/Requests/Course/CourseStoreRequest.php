<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class CourseStoreRequest extends FormRequest
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
            'schedule' => ['nullable', 'array'],
            'weighting' => ['required', 'array', 'size:3'], // Must be an array with 3 elements
            'weighting.homework' => ['required', 'numeric', 'min:0', 'max:1'],
            'weighting.midterms' => ['required', 'numeric', 'min:0', 'max:1'],
            'weighting.final_exam' => ['required', 'numeric', 'min:0', 'max:1'],
            'signature_id' => ['required', 'uuid', 'exists:signatures,id'],
            'semester_id' => ['required', 'uuid', 'exists:semesters,id'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $weighting = $this->input('weighting', []);
            $sum = array_sum($weighting);
            
            if (abs($sum - 1) > 0.0001) {
                $validator->errors()->add('weighting', 'The weighting values must add up to 1.');
            }
        });
    }

     /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'schedule.array' => 'The :attribute field must be an array.',

            'weighting.required' => 'The :attribute field is required.',
            'weighting.array' => 'The :attribute field must be an array.',
            'weighting.size' => 'The :attribute array must contain exactly 3 elements.',
            'weighting.homework.required' => 'The homework weighting is required.',
            'weighting.homework.numeric' => 'The homework weighting must be a numeric value.',
            'weighting.homework.min' => 'The homework weighting must be at least :min.',
            'weighting.homework.max' => 'The homework weighting must not exceed :max.',
            'weighting.midterms.required' => 'The midterms weighting is required.',
            'weighting.midterms.numeric' => 'The midterms weighting must be a numeric value.',
            'weighting.midterms.min' => 'The midterms weighting must be at least :min.',
            'weighting.midterms.max' => 'The midterms weighting must not exceed :max.',
            'weighting.final_exam.required' => 'The final exam weighting is required.',
            'weighting.final_exam.numeric' => 'The final exam weighting must be a numeric value.',
            'weighting.final_exam.min' => 'The final exam weighting must be at least :min.',
            'weighting.final_exam.max' => 'The final exam weighting must not exceed :max.',

            'signature_id.required' => 'The :attribute field is required.',
            'signature_id.exists' => 'The :attribute signature does not exist.',

            'semester_id.required' => 'The :attribute field is required.',
            'semester_id.exists' => 'The :attribute semester does not exist.',
        ];
    }
}
