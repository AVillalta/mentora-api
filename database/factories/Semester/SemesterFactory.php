<?php

namespace Database\Factories\Semester;

use App\Data\Semester\SemesterCalendarData;
use App\Models\Semester\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Semester\Semester>
 */
class SemesterFactory extends Factory
{
    protected $model = Semester::class;

    public function definition(): array
    {
        $currentDate = Carbon::now();
        $isActive = false;
        $year = $this->faker->numberBetween($currentDate->year - 2, $currentDate->year - 1); // Años pasados
        $semesterType = $this->faker->randomElement(['Primer Semestre', 'Segundo Semestre']);

        // Generar nombre base con un sufijo aleatorio para garantizar unicidad
        $baseName = "$year-" . ($year + 1) . " $semesterType";
        $name = $baseName . ' ' . Str::random(6); // Sufijo aleatorio
        while (Semester::where('name', $name)->exists()) {
            $name = "$baseName " . Str::random(6);
        }

        // Fechas para semestres no activos (pasados)
        $startDate = $semesterType === 'Primer Semestre'
            ? $this->faker->dateTimeBetween("$year-09-01", "$year-09-15")
            : $this->faker->dateTimeBetween("$year-02-01", "$year-02-15");
        $endDate = $semesterType === 'Primer Semestre'
            ? $this->faker->dateTimeBetween("$year-12-15", "$year-12-31")
            : $this->faker->dateTimeBetween("$year-06-15", "$year-06-30");

        $calendar = new SemesterCalendarData(
            holidays: [
                [
                    'name' => 'Navidad',
                    'start' => "$year-12-24",
                    'end' => ($year + 1) . "-01-06",
                ],
                [
                    'name' => 'Semana Santa',
                    'start' => "$year-04-10",
                    'end' => "$year-04-17",
                ],
            ],
            vacations: [
                [
                    'name' => 'Vacaciones de Invierno',
                    'start' => "$year-12-20",
                    'end' => ($year + 1) . "-01-08",
                ],
            ]
        );

        return [
            'name' => $name,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'calendar' => $calendar->toArray(),
            'is_active' => $isActive,
        ];
    }

    /**
     * Simulate an active semester with future end_date.
     *
     * @return static
     */
    public function active(): static
    {
        return $this->state(function (array $attributes) {
            $currentDate = Carbon::now();
            $year = $currentDate->year;
            $semesterType = $this->faker->randomElement(['Primer Semestre', 'Segundo Semestre']);

            // Generar nombre base con un sufijo aleatorio para garantizar unicidad
            $baseName = "$year-" . ($year + 1) . " $semesterType";
            $name = $baseName . ' ' . Str::random(6);
            while (Semester::where('name', $name)->exists()) {
                $name = "$baseName " . Str::random(6);
            }

            $startDate = $currentDate;
            $endDate = $semesterType === 'Primer Semestre'
                ? $this->faker->dateTimeBetween("$year-12-15", "$year-12-31")
                : $this->faker->dateTimeBetween(($year + 1) . "-06-15", ($year + 1) . "-06-30");

            return [
                'name' => $name,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'is_active' => true,
            ];
        });
    }
}