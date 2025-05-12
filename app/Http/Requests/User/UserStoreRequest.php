<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User\User;
use App\Rules\PhoneValidation;
use Illuminate\Validation\Rules;

class UserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        
        if ($this->has('role')) {
            $this->merge([
                'role' => strtolower($this->input('role')), 
            ]);
        }
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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone_number' => ['required', new PhoneValidation],
            'document' => ['required', 'string', 'max:15', 'unique:users,document'],
            'city' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:10'],
            'address' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'country_id' => ['required', 'exists:countries,id'],

            'role' => ['nullable', 'string', 'in:admin,professor,student'],
        ];
            //flag para ver que rol le pongo
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => 'name',
            'email' => 'email address',
            'password' => 'password',
            'phone_number' => 'phone number',
            'document' => 'document',
            'city' => 'city',
            'postal_code' => 'postal code',
            'address' => 'address',
            'date_of_birth' => 'date of birth',
            'country_id' => 'country',
            'role' => 'role',
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
            'name.string' => 'The :attribute must be a string.',
            'name.max' => 'The :attribute may not be greater than 255 characters.',

            'email.required' => 'The :attribute field is required.',
            'email.email' => 'The :attribute must be a valid email address.',
            'email.unique' => 'This :attribute is already taken.',

            'password.required' => 'The :attribute field is required.',
            'password.confirmed' => 'The :attribute confirmation does not match.',

            'phone_number.required' => 'The :attribute field is required.',
            'phone_number.string' => 'The :attribute must be a string.',
            
            'document.required' => 'The :attribute field is required.',
            'document.unique' => 'This :attribute already exists.',

            'city.required' => 'The :attribute field is required.',

            'postal_code.required' => 'The :attribute field is required.',

            'address.required' => 'The :attribute field is required.',

            'date_of_birth.required' => 'The :attribute field is required.',
            'date_of_birth.before' => 'The :attribute must be before today.',

            'country_id.required' => 'The :attribute field is required.',
            'country_id.exists' => 'The selected :attribute is invalid.',
            
            'role.in' => 'The :attribute must be one of the following values: admin, professor, student.',
        ];
    }
}
