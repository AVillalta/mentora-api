<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class CourseUpdateRequest extends FormRequest
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
            'schedule' => ['sometimes', 'array'],
            'weighting' => ['sometimes', 'array', 'size:3'], // Optional, must be an array with 3 elements
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
            'schedule.sometimes' => 'The :attribute field is optional, but if provided, it must be a valid JSON format.',
            'schedule.json' => 'The :attribute must be a valid JSON.',

            'weighting.sometimes' => 'The :attribute field is optional, but if provided, it must be an array.',
            'weighting.array' => 'The :attribute field must be an array.',
            'weighting.size' => 'The :attribute array must contain exactly 3 elements.',
            'weighting.*.required_with' => 'Each :attribute value (:attribute) is required when weighting is provided.',
            'weighting.*.numeric' => 'Each :attribute value (:attribute) must be numeric.',
            'weighting.*.min' => 'Each :attribute value (:attribute) must be at least :min.',
            'weighting.*.max' => 'Each :attribute value (:attribute) must not exceed :max.',
            
            'signature_id.sometimes' => 'The :attribute field is optional, but if provided, it must be a valid signature ID',
            'signature_id.exists' => 'The selected :attribute is invalid.',

            'semester_id.sometimes' => 'The :attribute field is optional, but if provided, it must be a valid semester ID.',
            'semester_id.exists' => 'The selected :attribute is invalid.'
        ];
    }
}
