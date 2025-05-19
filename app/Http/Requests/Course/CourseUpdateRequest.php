<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['sometimes', 'string', 'max:20', Rule::unique('courses')->ignore($this->course)],
            'schedule' => ['sometimes', 'array'],
            'weighting' => ['sometimes', 'array', 'size:3'],
            'weighting.homework' => ['required_with:weighting', 'numeric', 'min:0', 'max:1'],
            'weighting.midterms' => ['required_with:weighting', 'numeric', 'min:0', 'max:1'],
            'weighting.final_exam' => ['sometimes', 'numeric', 'min:0', 'max:1'],
            'signature_id' => ['sometimes', 'uuid', 'exists:signatures,id'],
            'semester_id' => ['sometimes', 'uuid', 'exists:semesters,id'],
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

    public function messages(): array
    {
        return [
            'code.sometimes' => 'The code field is optional, but if provided, it must be a string.',
            'code.string' => 'The code must be a string.',
            'code.max' => 'The code may not be greater than 20 characters.',
            'code.unique' => 'The code has already been taken.',
            'schedule.sometimes' => 'The schedule field is optional, but if provided, it must be an array.',
            'weighting.sometimes' => 'The weighting field is optional, but if provided, it must be an array.',
            'weighting.array' => 'The weighting field must be an array.',
            'weighting.size' => 'The weighting array must contain exactly 3 elements.',
            'weighting.*.required_with' => 'Each weighting value (:attribute) is required when weighting is provided.',
            'weighting.*.numeric' => 'Each weighting value (:attribute) must be numeric.',
            'weighting.*.min' => 'Each weighting value (:attribute) must be at least :min.',
            'weighting.*.max' => 'Each weighting value (:attribute) must not exceed :max.',
            'signature_id.sometimes' => 'The signature field is optional, but if provided, it must be a valid signature ID.',
            'signature_id.exists' => 'The selected signature is invalid.',
            'semester_id.sometimes' => 'The semester field is optional, but if provided, it must be a valid semester ID.',
            'semester_id.exists' => 'The selected semester is invalid.',
        ];
    }
}