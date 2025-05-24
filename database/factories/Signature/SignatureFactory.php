<?php

namespace Database\Factories\Signature;

use App\Models\Signature\Signature;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Signature\Signature>
 */
class SignatureFactory extends Factory
{
    protected $model = Signature::class;

    public function definition(): array
    {
        $subjects = [
            'Matemáticas', 'Física', 'Programación', 'Bases de Datos', 'Inteligencia Artificial',
            'Literatura Española', 'Historia Contemporánea', 'Biología Molecular', 'Química Orgánica',
            'Inglés Académico', 'Economía', 'Estadística', 'Álgebra Lineal', 'Redes de Computadoras',
            'Diseño Gráfico', 'Psicología', 'Sociología', 'Derecho Constitucional',
        ];

        $professor = User::whereHas('roles', fn ($query) => $query->where('name', 'professor'))
                         ->inRandomOrder()
                         ->first() ?? User::factory()->professor()->create();

        return [
            'name' => $this->faker->randomElement($subjects) . ' ' . $this->faker->randomElement(['I', 'II', 'III', 'Avanzado', 'Básico']),
            'syllabus' => [
                'objectives' => [
                    $this->faker->sentence(),
                    $this->faker->sentence(),
                ],
                'topics' => [
                    $this->faker->word() => $this->faker->sentence(),
                    $this->faker->word() => $this->faker->sentence(),
                    $this->faker->word() => $this->faker->sentence(),
                ],
                'evaluation' => [
                    'exams' => $this->faker->numberBetween(40, 70) . '%',
                    'assignments' => $this->faker->numberBetween(20, 40) . '%',
                    'participation' => $this->faker->numberBetween(10, 20) . '%',
                ],
            ],
            'professor_id' => $professor->id,
        ];
    }

    public function withSyllabusPdf(): static
    {
        return $this->afterCreating(function (Signature $signature) {
            // Simular un PDF subiendo un archivo de prueba o generando un PDF temporal
            $signature->addMediaFromUrl('https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf')
                      ->toMediaCollection('syllabus_pdf');
        });
    }

    public function withoutProfessor(): static
    {
        return $this->state(fn (array $attributes) => [
            'professor_id' => null,
        ]);
    }
}