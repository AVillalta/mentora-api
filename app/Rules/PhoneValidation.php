<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneValidation implements ValidationRule
{

   /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Clean the phone number by removing unwanted characters
        $value = str_replace(array(" ", ".", ",", ":", ";", "'", "", "-", "#", "(", ")", "{", "}", "[", "]",), '', strval($value));


         // Si está vacío después de limpiar, error
         if (empty($value)) {
            $fail("El :attribute es inválido.");
            return;
         }
        
        // Si el número no empieza con "+", se le añade el código de España (+34)
        if (!preg_match('/^\+/', $value)) {
            $value = '+34' . $value;
        }

        // Validar el formato E.164
        if (!preg_match('/^\+[1-9]\d{6,14}$/', $value)) {
            $fail("El :attribute debe ser un número de teléfono válido en formato E.164. Ejemplo: +34123456789");
        }
    }
}