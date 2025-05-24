<?php

namespace Database\Factories\Assignment;

use App\Models\Assignment\Assignment;
use App\Models\Course\Course;
use App\Models\Enrollment\Enrollment;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assignment\Assignment>
 */
class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;

    public function definition(): array
    {
        $course = Course::inRandomOrder()->first() ?? Course::factory()->create();
        $totalStudents = Enrollment::where('course_id', $course->id)->count();

        $semester = $course->semester;
        $currentDate = Carbon::now();
        if ($semester) {
            $startDate = Carbon::parse($semester->start_date)->max($currentDate);
            $endDate = Carbon::parse($semester->end_date)->max($startDate->copy()->addDay());
            $dueDate = $this->faker->dateTimeBetween($startDate, $endDate);
        } else {
            $dueDate = $this->faker->dateTimeBetween($currentDate, $currentDate->copy()->addMonths(2));
        }

        $submissions = 0; // Inicializar en 0, actualizar en withEnrollments

        return [
            'title' => $this->faker->randomElement([
                'Tarea', 'Proyecto', 'Ejercicio', 'Práctica', 'Trabajo'
            ]) . ' ' . $this->faker->numberBetween(1, 10) . ': ' . $this->faker->words(3, true),
            'description' => $this->faker->paragraph(2),
            'course_id' => $course->id,
            'due_date' => $dueDate->format('Y-m-d'),
            'submissions' => $submissions,
            'total_students' => $totalStudents,
        ];
    }

    /**
     * Añadir matrículas para el curso si no existen y actualizar submissions.
     *
     * @return static
     */
    public function withEnrollments(): static
    {
        return $this->afterCreating(function (Assignment $assignment) {
            $course = Course::find($assignment->course_id);
            $totalStudents = Enrollment::where('course_id', $course->id)->count();

            if ($totalStudents === 0) {
                $existingStudents = User::whereHas('roles', fn ($query) => $query->where('name', 'student'))
                                       ->inRandomOrder()
                                       ->take($this->faker->numberBetween(5, 10))
                                       ->get();
                if ($existingStudents->isEmpty()) {
                    $existingStudents = User::factory()->student()->count(5)->create();
                }
                foreach ($existingStudents as $student) {
                    Enrollment::factory()->create([
                        'course_id' => $course->id,
                        'student_id' => $student->id,
                    ]);
                }
                $assignment->total_students = $existingStudents->count();
                $assignment->submissions = $this->faker->numberBetween(ceil($assignment->total_students * 0.7), $assignment->total_students);
                $assignment->save();
            } else {
                $assignment->total_students = $totalStudents;
                $assignment->submissions = $this->faker->numberBetween(ceil($totalStudents * 0.7), $totalStudents);
                $assignment->save();
            }
        });
    }

    /**
     * Simulate submissions with reliable external URLs.
     *
     * @return static
     */
    public function withSubmissions(): static
    {
        return $this->afterCreating(function (Assignment $assignment) {
            $enrollments = Enrollment::where('course_id', $assignment->course_id)
                                    ->inRandomOrder()
                                    ->take($assignment->submissions)
                                    ->get();

            foreach ($enrollments as $enrollment) {
                $fileUrl = 'https://www.irs.gov/pub/irs-pdf/n1036.pdf'; // Solo PDF
                try {
                    $assignment->addMediaFromUrl($fileUrl)
                               ->withCustomProperties(['student_id' => $enrollment->student_id])
                               ->toMediaCollection('assignment_submissions');
                } catch (\Exception $e) {
                    Log::warning("No se pudo añadir medio para tarea {$assignment->id}: {$e->getMessage()}");
                }
            }
        });
    }

    /**
     * Simulate an overdue assignment.
     *
     * @return static
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => $this->faker->dateTimeBetween('-3 months', '-1 week')->format('Y-m-d'),
        ]);
    }
}