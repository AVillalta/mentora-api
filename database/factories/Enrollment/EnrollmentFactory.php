<?php

namespace Database\Factories\Enrollment;

use App\Models\Assignment\Assignment;
use App\Models\Course\Course;
use App\Models\Enrollment\Enrollment;
use App\Models\Grade\Grade;
use App\Models\Semester\Semester;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enrollment\Enrollment>
 */
class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition(): array
    {
        $course = Course::inRandomOrder()->first() ?? Course::factory()->create();
        $student = User::whereHas('roles', fn ($query) => $query->where('name', 'student'))
                       ->inRandomOrder()
                       ->first() ?? User::factory()->student()->create();

        $semester = $course->semester;
        $enrollmentDate = $semester
            ? $this->faker->dateTimeBetween($semester->start_date, Carbon::parse($semester->start_date)->addDays(30))
            : $this->faker->dateTimeThisYear();

        return [
            'student_id' => $student->id,
            'course_id' => $course->id,
            'enrollment_date' => $enrollmentDate->format('Y-m-d'),
            'final_grade' => null,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Enrollment $enrollment) {
            $isActiveSemester = $enrollment->course->semester->is_active;
            $assignments = Assignment::where('course_id', $enrollment->course_id)->get();

            if ($assignments->isEmpty()) {
                // Crear al menos una tarea si no existe
                $assignment = Assignment::factory()->create([
                    'course_id' => $enrollment->course_id,
                    'total_students' => Enrollment::where('course_id', $enrollment->course_id)->count(),
                    'submissions' => $this->faker->numberBetween(ceil(Enrollment::where('course_id', $enrollment->course_id)->count() * 0.7), Enrollment::where('course_id', $enrollment->course_id)->count()),
                ]);
                $assignments = collect([$assignment]);
            }

            foreach ($assignments as $assignment) {
                if ($this->faker->boolean(80)) {
                    Grade::factory()->create([
                        'enrollment_id' => $enrollment->id,
                        'assignment_id' => $assignment->id,
                        'grade_type' => $isActiveSemester
                            ? $this->faker->randomElement(['partial', 'work', 'final'])
                            : $this->faker->randomElement(['ordinary', 'extraordinary', 'work', 'partial', 'final']),
                    ]);
                }
            }

            $grades = Grade::where('enrollment_id', $enrollment->id)->get();
            if ($grades->isNotEmpty() && !$isActiveSemester) {
                $enrollment->final_grade = $grades->avg('grade_value');
                $enrollment->save();
            }
        });
    }
}