<?php

namespace Database\Factories\Grade;

use App\Models\Assignment\Assignment;
use App\Models\Enrollment\Enrollment;
use App\Models\Grade\Grade;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Grade\Grade>
 */
class GradeFactory extends Factory
{
    protected $model = Grade::class;

    public function definition(): array
    {
        $enrollment = Enrollment::inRandomOrder()->first() ?? Enrollment::factory()->create();
        $assignment = Assignment::where('course_id', $enrollment->course_id)->inRandomOrder()->first() ?? Assignment::factory()->create([
            'course_id' => $enrollment->course_id,
        ]);

        $dueDate = Carbon::parse($assignment->due_date);
        $gradeDate = $this->faker->dateTimeBetween($dueDate, $dueDate->copy()->addDays(7));

        return [
            'grade_type' => $this->faker->randomElement(['ordinary', 'extraordinary', 'work', 'partial', 'final']),
            'title' => $this->faker->optional(0.7)->sentence(3),
            'grade_value' => $this->faker->randomFloat(2, 0, 10),
            'grade_date' => $gradeDate->format('Y-m-d'),
            'enrollment_id' => $enrollment->id,
            'assignment_id' => $assignment->id,
        ];
    }
}