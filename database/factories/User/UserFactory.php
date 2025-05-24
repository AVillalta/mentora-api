<?php

namespace Database\Factories\User;

use App\Models\Country\Country;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        $fullName = "$firstName $lastName";
        $email = $this->faker->unique()->safeEmail;

        $documentType = $this->faker->randomElement(['DNI', 'NIE']);
        $document = $documentType === 'DNI'
            ? $this->faker->unique()->numerify('########') . $this->faker->randomLetter()
            : $this->faker->randomElement(['X', 'Y', 'Z']) . $this->faker->unique()->numerify('#######') . $this->faker->randomLetter();

        $country = Country::inRandomOrder()->first() ?? Country::factory()->create();
        $city = $this->faker->city();

        return [
            'name' => $fullName,
            'email' => $email,
            'email_verified_at' => $this->faker->optional(0.9, null)->dateTimeThisYear(),
            'password' => static::$password ??= Hash::make('password1234'),
            'phone_number' => $this->faker->e164PhoneNumber(),
            'document' => $document,
            'city' => $city,
            'postal_code' => $this->faker->postcode(),
            'address' => $this->faker->streetAddress(),
            'date_of_birth' => $this->faker->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'country_id' => $country->id,
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_of_birth' => $this->faker->dateTimeBetween('-50 years', '-30 years')->format('Y-m-d'),
        ])->afterCreating(function (User $user) {
            try {
                $user->withoutRelations()->assignRole('admin');
            } catch (\Exception $e) {
                Log::warning("No se pudo asignar rol 'admin' al usuario {$user->id}: {$e->getMessage()}");
            }
        });
    }

    public function professor(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_of_birth' => $this->faker->dateTimeBetween('-60 years', '-30 years')->format('Y-m-d'),
        ])->afterCreating(function (User $user) {
            try {
                $user->withoutRelations()->assignRole('professor');
            } catch (\Exception $e) {
                Log::warning("No se pudo asignar rol 'professor' al usuario {$user->id}: {$e->getMessage()}");
            }
        });
    }

    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_of_birth' => $this->faker->dateTimeBetween('-25 years', '-16 years')->format('Y-m-d'),
        ])->afterCreating(function (User $user) {
            try {
                $user->withoutRelations()->assignRole('student');
            } catch (\Exception $e) {
                Log::warning("No se pudo asignar rol 'student' al usuario {$user->id}: {$e->getMessage()}");
            }
        });
    }

    public function withProfilePhoto(): static
    {
        return $this->afterCreating(function (User $user) {
            try {
                $user->addMediaFromUrl('https://picsum.photos/200')->toMediaCollection('profile_photo');
            } catch (\Exception $e) {
                Log::warning("No se pudo añadir foto de perfil para usuario {$user->id}: {$e->getMessage()}");
            }
        });
    }
}