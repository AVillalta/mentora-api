<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User\User;
use App\Rules\PhoneValidation;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
        $userId = $this->route('id');
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($userId)],//ignore($this->user()->id)
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique("users")->ignore($userId)], // Permitir el correo electrónico actual
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()], // Si el campo de contraseña es nulo, no se valida
            'phone_number' => ['nullable', new PhoneValidation],
            'document' => ['nullable', 'string', 'max:15', Rule::unique('users')->ignore($userId)], // Permitir el documento actual
            'city' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'address' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'country_id' => ['nullable', 'exists:countries,id'],

            'role' => ['nullable', 'string', 'in:admin,professor,student'],
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
            'name.unique' => 'This :attribute is already taken.',
            
            'email.required' => 'The :attribute field is required.',
            'email.string' => 'The :attribute must be a string.',
            'email.email' => 'The :attribute must be a valid email address.',
            'email.max' => 'The :attribute may not be greater than 255 characters.',
            'email.unique' => 'This :attribute is already taken.',
            
            'password.nullable' => 'The :attribute field is optional.',
            'password.confirmed' => 'The :attribute confirmation does not match.',
            'password.default' => 'The :attribute does not meet the required criteria.',
            
            'phone_number.nullable' => 'The :attribute field is optional.',
            'phone_number.string' => 'The :attribute must be a string.',
            
            'document.nullable' => 'The :attribute field is optional.',
            'document.string' => 'The :attribute must be a string.',
            'document.max' => 'The :attribute may not be greater than 15 characters.',
            'document.unique' => 'This :attribute already exists.',
            
            'city.nullable' => 'The :attribute field is optional.',
            'city.string' => 'The :attribute must be a string.',
            'city.max' => 'The :attribute may not be greater than 255 characters.',
            
            'postal_code.nullable' => 'The :attribute field is optional.',
            'postal_code.string' => 'The :attribute must be a string.',
            'postal_code.max' => 'The :attribute may not be greater than 10 characters.',
            
            'address.nullable' => 'The :attribute field is optional.',
            'address.string' => 'The :attribute must be a string.',
            'address.max' => 'The :attribute may not be greater than 255 characters.',
            
            'date_of_birth.nullable' => 'The :attribute field is optional.',
            'date_of_birth.date' => 'The :attribute must be a valid date.',
            'date_of_birth.before' => 'The :attribute must be before today.',
            
            'country_id.nullable' => 'The :attribute field is optional.',
            'country_id.exists' => 'The selected :attribute is invalid.',
            
            'role.nullable' => 'The :attribute field is optional.',
            'role.in' => 'The :attribute must be one of the following values: admin, professor, student.',
        ];
    }
}
