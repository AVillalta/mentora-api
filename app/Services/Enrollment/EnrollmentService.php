<?php

namespace App\Services\Enrollment;

use App\Models\Enrollment\Enrollment;
use App\Models\Signature\Signature;
use App\Models\User\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use PhpParser\Node\Expr\FuncCall;

class EnrollmentService{

    /**
     * get all enrollments.
     *
     * @param
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Enrollment[]
     */
    public function getAllEnrollments() {
        return Enrollment::all();
    }

     /**
     * Create new enrollment.
     *
     * @param array $data
     * @return \App\Models\Enrollment
     */
    public function saveEnrollment(array $data) {

        if (!auth()->user()->can('create-enrollments')) {
            return [
                'example' => false,
                'httpStatus' => Response::HTTP_FORBIDDEN,
                'message' => "No tienes permiso para realizar esta acción."
            ];
        }

        return DB::transaction(function() use ($data){
            return Enrollment::create([
                'enrollment_date' => $data['enrollment_date'],
                'course_id' => $data['course_id'],
                'student_id' => $data['student_id'],
            ]);
        });
    }

    /**
     * Get enrollment by id.
     *
     * @param $data
     * @return \App\Models\Enrollment
     */
    public function showEnrollment($data)
    {
        $result = Enrollment::findOrFail($data["enrollment_id"]);
        return $result;
    }

     /**
     * Update enrollment.
     *
     * @param array $data
     * @return \App\Models\Enrollment
     */
    public function updateEnrollment(array $data, $id) {

        $enrollment = Enrollment::findOrFail($id);

        return DB::transaction(function() use ($enrollment, $data){
            $updates =  [
                'enrollment_date' => $data['enrollment_date'] ?? $enrollment->enrollment_date,
                'course_id' => $data['course_id'] ?? $enrollment->course_id,
                'student_id' => $data['student_id'] ?? $enrollment->student_id
            ];
            $enrollment->update($updates);
            return $enrollment;
        });
    }

    public function deleteEnrollment($id)
    {
        DB::transaction(function() use ($id) {
            $signature = Enrollment::findOrFail($id);
            $signature->delete();
        });
    }

}