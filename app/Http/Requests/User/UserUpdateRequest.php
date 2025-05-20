<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User\User;
use App\Rules\PhoneValidation;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

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
        // Intentar obtener el ID desde la ruta
        $userId = $this->route('id');

        // Depuración detallada
        Log::debug('UserUpdateRequest: Datos de la solicitud', [
            'userId' => $userId,
            'routeParameters' => $this->route()->parameters(),
            'input' => $this->all(),
            'routeName' => $this->route()->getName(),
        ]);

        if (!$userId) {
            Log::error('UserUpdateRequest: No se pudo obtener userId desde $this->route("id")');
            // Intentar obtener el ID desde el parámetro 'user' o 'id' como string
            $user = $this->route('user') ?? $this->route('id');
            $userId = is_string($user) ? $user : ($user instanceof User ? $user->id : null);
            Log::debug('UserUpdateRequest: Intentando userId desde $this->route("user") o $this->route("id")', [
                'userId' => $userId,
                'userType' => gettype($user),
                'userValue' => $user,
            ]);
        }

        if (!$userId) {
            Log::error('UserUpdateRequest: No se encontró userId válido, usando validación sin unicidad');
            return [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
                'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
                'phone_number' => ['nullable', new PhoneValidation],
                'document' => ['nullable', 'string', 'max:15'],
                'city' => ['nullable', 'string', 'max:255'],
                'postal_code' => ['nullable', 'string', 'max:10'],
                'address' => ['nullable', 'string', 'max:255'],
                'date_of_birth' => ['nullable', 'date', 'before:today'],
                'country_id' => ['nullable', 'exists:countries,id'],
                'role' => ['nullable', 'string', 'in:admin,professor,student'],
            ];
        }

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('users', 'name')->ignore($userId, 'id')],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId, 'id')],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'phone_number' => ['nullable', new PhoneValidation],
            'document' => ['nullable', 'string', 'max:15', Rule::unique('users', 'document')->ignore($userId, 'id')],
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
            'name.required' => 'El campo nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede exceder los 255 caracteres.',
            'name.unique' => 'Este nombre ya está en uso.',
            
            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.string' => 'El correo electrónico debe ser una cadena de texto.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.max' => 'El correo electrónico no puede exceder los 255 caracteres.',
            'email.unique' => 'Este correo electrónico ya está en uso.',
            
            'password.nullable' => 'El campo contraseña es opcional.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.default' => 'La contraseña debe tener al menos 8 caracteres.',
            
            'phone_number.nullable' => 'El campo número de teléfono es opcional.',
            'phone_number.string' => 'El número de teléfono debe ser una cadena de texto.',
            
            'document.nullable' => 'El campo documento es opcional.',
            'document.string' => 'El documento debe ser una cadena de texto.',
            'document.max' => 'El documento no puede exceder los 15 caracteres.',
            'document.unique' => 'Este documento ya está en uso.',
            
            'city.nullable' => 'El campo ciudad es opcional.',
            'city.string' => 'La ciudad debe ser una cadena de texto.',
            'city.max' => 'La ciudad no puede exceder los 255 caracteres.',
            
            'postal_code.nullable' => 'El campo código postal es opcional.',
            'postal_code.string' => 'El código postal debe ser una cadena de texto.',
            'postal_code.max' => 'El código postal no puede exceder los 10 caracteres.',
            
            'address.nullable' => 'El campo dirección es opcional.',
            'address.string' => 'La dirección debe ser una cadena de texto.',
            'address.max' => 'La dirección no puede exceder los 255 caracteres.',
            
            'date_of_birth.nullable' => 'El campo fecha de nacimiento es opcional.',
            'date_of_birth.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'date_of_birth.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            
            'country_id.nullable' => 'El campo país es opcional.',
            'country_id.exists' => 'El país seleccionado no es válido.',
            
            'role.nullable' => 'El campo rol es opcional.',
            'role.in' => 'El rol debe ser uno de los siguientes valores: admin, professor, student.',
        ];
    }
}