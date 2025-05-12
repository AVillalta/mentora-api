<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\User\User;

class RoleValidation implements ValidationRule
{

    protected $role;

    /**
     * Create a new rule instance.
     *
     * @param string $role
     */
    public function __construct(string $role)
    {
        $this->role = $role;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = User::find($value);
        if (!$user) {
            $fail('The selected :attribute is invalid.');
        } elseif (!$user->hasRole($this->role)) {
            $fail("The selected {$attribute} does not belong to a user with the {$this->role} role.");
        }
    }
}
