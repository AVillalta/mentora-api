<?php

namespace App\Services\Course;

use App\Models\Course\Course;
use Illuminate\Support\Facades\DB;

class CourseService
{
    public function getAllCourses()
    {
        return Course::all();
    }

    public function saveCourse(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Course::create([
                'code' => $data['code'],
                'schedule' => $data['schedule'],
                'weighting' => $data['weighting'],
                'signature_id' => $data['signature_id'],
                'semester_id' => $data['semester_id'],
            ]);
        });
    }

    public function showCourse($data)
    {
        $course = Course::where('id', $data["course_id"])->firstOrFail();
        if (auth()->user()->hasRole('student')) {
            if (!$course->enrollments()->where('student_id', auth()->id())->exists()) {
                abort(403, 'User does not have the right roles');
            }
        }
        return $course;
    }

    public function updateCourse(array $data, $id)
    {
        $course = Course::findOrFail($id);
        return DB::transaction(function () use ($course, $data) {
            $updates = [
                'code' => $data['code'] ?? $course->code,
                'schedule' => $data['schedule'] ?? $course->schedule,
                'weighting' => $data['weighting'] ?? $course->weighting,
                'signature_id' => $data['signature_id'] ?? $course->signature_id,
                'semester_id' => $data['semester_id'] ?? $course->semester_id,
            ];
            $course->update($updates);
            return $course;
        });
    }

    public function deleteCourse($id)
    {
        return DB::transaction(function () use ($id) {
            $course = Course::findOrFail($id);
            $course->delete();
        });
    }
}