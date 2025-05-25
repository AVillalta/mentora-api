<?php

namespace App\Services\Assignment;

use App\Models\Assignment\Assignment;
use App\Models\Enrollment\Enrollment;
use App\Models\User\User;
use App\Models\Grade\Grade;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class AssignmentService
{
    public function getAllAssignments()
    {
        $user = auth()->user();
        $assignments = Assignment::query();

        if ($user->hasRole('professor')) {
            // Filtrar tareas por cursos asignados al profesor y activos
            $assignments->whereHas('course.signature', function (Builder $query) use ($user) {
                $query->where('professor_id', $user->id);
            })->whereHas('course.semester', function (Builder $query) {
                $query->where('is_active', true);
            });
        } elseif ($user->hasRole('student')) {
            $enrolledCourseIds = Enrollment::where('student_id', $user->id)->pluck('course_id');
            $assignments->whereIn('course_id', $enrolledCourseIds);
        }

        return $assignments->get()->map(function ($assignment) {
            $assignment->total_students = Enrollment::where('course_id', $assignment->course_id)->count();
            $assignment->submissions = $assignment->getMedia('assignment_submissions')
                ->pluck('custom_properties.student_id')
                ->unique()
                ->count();
            return $assignment;
        });
    }

    public function saveAssignment(array $data)
    {
        return DB::transaction(function () use ($data) {
            $totalStudents = Enrollment::where('course_id', $data['course_id'])->count();
            return Assignment::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'course_id' => $data['course_id'],
                'due_date' => $data['due_date'],
                'submissions' => 0,
                'total_students' => $totalStudents,
            ]);
        });
    }

    public function submitAssignment(Assignment $assignment, UploadedFile $file, User $student)
    {
        return DB::transaction(function () use ($assignment, $file, $student) {
            $enrollment = Enrollment::where('student_id', $student->id)
                                    ->where('course_id', $assignment->course_id)
                                    ->first();

            if (!$enrollment) {
                throw new \Exception("No estás matriculado en este curso.");
            }

            $existingSubmission = $assignment->getMedia('assignment_submissions')
                ->where('custom_properties.student_id', $student->id)
                ->first();

            $submission = $assignment->addSubmission($file, $student);

            Grade::updateOrCreate(
                [
                    'enrollment_id' => $enrollment->id,
                    'assignment_id' => $assignment->id,
                ],
                [
                    'title' => $assignment->title,
                    'grade_type' => 'work',
                    'grade_value' => null,
                    'grade_date' => now(),
                    'course_name' => $assignment->course->signature ?? 'Unknown Course',
                ]
            );

            if (!$existingSubmission) {
                $assignment->increment('submissions');
            }

            // Verificar que no haya notas duplicadas
            $gradeCount = Grade::where('assignment_id', $assignment->id)
                               ->where('enrollment_id', $enrollment->id)
                               ->count();
            if ($gradeCount > 1) {
                throw new \Exception("Se detectaron notas duplicadas para el estudiante.");
            }

            return $submission;
        });
    }

    public function showAssignment($data)
    {
        $assignment = Assignment::findOrFail($data["assignment_id"]);
        $assignment->total_students = Enrollment::where('course_id', $assignment->course_id)->count();
        $assignment->submissions = $assignment->getMedia('assignment_submissions')
            ->pluck('custom_properties.student_id')
            ->unique()
            ->count();
        return $assignment;
    }

    public function updateAssignment(array $data, $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $assignment = Assignment::findOrFail($id);

            $totalStudents = Enrollment::where('course_id', $data['course_id'] ?? $assignment->course_id)->count();

            $updates = [
                'title' => $data['title'] ?? $assignment->title,
                'description' => $data['description'] ?? $assignment->description,
                'course_id' => $data['course_id'] ?? $assignment->course_id,
                'due_date' => $data['due_date'] ?? $assignment->due_date,
                'submissions' => $assignment->getMedia('assignment_submissions')
                    ->pluck('custom_properties.student_id')
                    ->unique()
                    ->count(),
                'total_students' => $totalStudents,
            ];

            $assignment->update($updates);
            return $assignment;
        });
    }

    public function deleteAssignment($id)
    {
        return DB::transaction(function () use ($id) {
            $assignment = Assignment::findOrFail($id);
            $assignment->clearMediaCollection('assignment_submissions');
            $assignment->delete();
        });
    }

    public function gradeSubmission(Assignment $assignment, string $submissionId, float $gradeValue, User $student)
    {
        return DB::transaction(function () use ($assignment, $submissionId, $gradeValue, $student) {
            $enrollment = Enrollment::where('student_id', $student->id)
                                    ->where('course_id', $assignment->course_id)
                                    ->firstOrFail();

            $grade = Grade::where('assignment_id', $assignment->id)
                          ->where('enrollment_id', $enrollment->id)
                          ->first();

            if (!$grade) {
                throw new \Exception("No se encontró una nota para esta entrega.");
            }

            $grade->update([
                'grade_value' => $gradeValue,
                'grade_date' => now(),
            ]);

            // Verificar que no haya notas duplicadas
            $gradeCount = Grade::where('assignment_id', $assignment->id)
                               ->where('enrollment_id', $enrollment->id)
                               ->count();
            if ($gradeCount > 1) {
                throw new \Exception("Se detectaron notas duplicadas para el estudiante.");
            }

            return $grade;
        });
    }
}