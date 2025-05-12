<?php

namespace App\Services\Course;

use App\Models\Course\Course;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use PhpParser\Node\Expr\FuncCall;

class CourseService{

    /**
     * get all courses.
     *
     * @param
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Course[]
     */
    public function getAllCourses() {
        return Course::all();
    }

     /**
     * Create new course.
     *
     * @param array $data
     * @return \App\Models\Course
     */
    public function saveCourse(array $data) {

        return DB::transaction(function() use ($data){
            return Course::create([
                'schedule' => $data['schedule'],
                'weighting' => $data['weighting'],
                'signature_id' => $data['signature_id'],
                'semester_id' => $data['semester_id'],
            ]);
        });
    }

    /**
     * Get course by id.
     *
     * @param $data
     * @return \App\Models\Course
     */
    public function showCourse($data)
    {
        $result = Course::findOrFail($data["course_id"]);
        return $result;
    }

     /**
     * Update course.
     *
     * @param array $data
     * @return \App\Models\Course
     */
    public function updateCourse(array $data, $id) {

        $course = Course::findOrFail($id);

        return DB::transaction(function() use ($course, $data){
            $updates =  [
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
        DB::transaction(function() use ($id) {
            $course = Course::findOrFail($id);
            $course->delete();
        });
    }

}