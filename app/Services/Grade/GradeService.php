<?php

namespace App\Services\Grade;

use App\Models\Course\Course;
use App\Models\Grade\Grade;
use App\Models\Enrollment\Enrollment;
use Illuminate\Support\Facades\DB;

class GradeService
{
    /**
     * Get all grades, filtered by user role.
     *
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Grade[]
     */
    public function getAllGrades()
    {
        $user = auth()->user();

        if ($user && $user->hasRole('student')) {
            return Grade::whereHas('enrollment', function ($query) use ($user) {
                $query->where('student_id', $user->id);
            })->get();
        } elseif ($user && $user->hasRole('professor')) {
            return Grade::whereHas('enrollment.course.signature', function ($query) use ($user) {
                $query->where('professor_id', $user->id);
            })->get();
        }

        return Grade::all();
    }

    /**
     * Create new grade.
     *
     * @param array $data
     * @return \App\Models\Grade
     */
    public function saveGrade(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Grade::create([
                'title' => $data['title'],
                'grade_type' => $data['grade_type'],
                'grade_value' => $data['grade_value'],
                'grade_date' => $data['grade_date'],
                'enrollment_id' => $data['enrollment_id'],
            ]);
        });
    }

    /**
     * Get grade by id.
     *
     * @param array $data
     * @return \App\Models\Grade
     */
    public function showGrade($data)
    {
        $result = Grade::findOrFail($data["grade_id"]);
        return $result;
    }

    /**
     * Update grade.
     *
     * @param array $data
     * @return \App\Models\Grade
     */
    public function updateGrade(array $data, $id)
    {
        $grade = Grade::findOrFail($id);

        return DB::transaction(function () use ($grade, $data) {
            $updates = [
                'title' => $data['title'] ?? $grade->title,
                'grade_type' => $data['grade_type'] ?? $grade->grade_type,
                'grade_value' => $data['grade_value'] ?? $grade->grade_value,
                'grade_date' => $data['grade_date'] ?? $grade->grade_date,
                'enrollment_id' => $data['enrollment_id'] ?? $grade->enrollment_id,
            ];
            $grade->update($updates);
            return $grade;
        });
    }

    /**
     * Delete grade.
     *
     * @param string $id
     * @return void
     */
    public function deleteGrade($id)
    {
        DB::transaction(function () use ($id) {
            $grade = Grade::findOrFail($id);
            $grade->delete();
        });
    }
}