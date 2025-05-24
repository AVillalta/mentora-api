<?php

namespace Database\Factories\Course;

use App\Models\Course\Course;
use App\Models\Semester\Semester;
use App\Models\Signature\Signature;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course\Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        $semester = Semester::inRandomOrder()->first() ?? Semester::factory()->create();
        $signature = Signature::inRandomOrder()->first() ?? Signature::factory()->create();

        // Generar ponderaciones que sumen exactamente 1
        $homework = $this->faker->randomFloat(2, 0.2, 0.5);
        $midterms = $this->faker->randomFloat(2, 0.2, 0.5);
        $finalExam = round(1 - $homework - $midterms, 2);

        // Asegurar que la suma sea exactamente 1
        if ($finalExam < 0) {
            $homework = 0.3;
            $midterms = 0.3;
            $finalExam = 0.4;
        }

        return [
            'code' => $this->faker->unique()->regexify('[A-Z]{2,4}\d{3}-[A-Z]'),
            'schedule' => [
                [
                    'day' => $this->faker->randomElement(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes']),
                    'start_time' => $this->faker->randomElement(['08:00', '10:00', '12:00', '14:00']),
                    'end_time' => $this->faker->randomElement(['10:00', '12:00', '14:00', '16:00']),
                ],
                [
                    'day' => $this->faker->randomElement(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes']),
                    'start_time' => $this->faker->randomElement(['08:00', '10:00', '12:00', '14:00']),
                    'end_time' => $this->faker->randomElement(['10:00', '12:00', '14:00', '16:00']),
                ],
            ],
            'weighting' => [
                'homework' => $homework,
                'midterms' => $midterms,
                'final_exam' => $finalExam,
            ],
            'signature_id' => $signature->id,
            'semester_id' => $semester->id,
        ];
    }

    public function withoutSchedule(): static
    {
        return $this->state(fn (array $attributes) => [
            'schedule' => null,
        ]);
    }

    public function activeSemester(): static
    {
        return $this->state(fn (array $attributes) => [
            'semester_id' => Semester::where('is_active', true)->inRandomOrder()->first()->id
                           ?? Semester::factory()->active()->create()->id,
        ]);
    }
}