<?php

namespace App\Services\Semester;

use App\Models\Semester\Semester;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use PhpParser\Node\Expr\FuncCall;

class SemesterService{

    /**
     * get all semesters.
     *
     * @param
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Semester[]
     */
    public function getAllSemesters() {
        return Semester::all();
    }

     /**
     * Create new semester.
     *
     * @param array $data
     * @return \App\Models\Semester
     */
    public function saveSemester(array $data) {

        return DB::transaction(function() use ($data){
            return Semester::create([
                'name' => $data['name'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'calendar' => $data['calendar']
            ]);
        });
    }

    /**
     * Get semester by id.
     *
     * @param $data
     * @return \App\Models\Semester
     */
    public function showSemester($data)
    {
        $result = Semester::findOrFail($data["Semester_id"]);
        return $result;
    }

     /**
     * Update semester.
     *
     * @param array $data
     * @return \App\Models\Semester
     */
    public function updateSemester(array $data, $id) {

        $semester = Semester::findOrFail($id);

        return DB::transaction(function() use ($semester, $data){
            $updates =  [
                'name' => $data['name'] ?? $semester->name,
                'start_date' =>  $data['start_date'] ?? $semester->start_date,
                'end_date' => $data['end_date'] ?? $semester->end_date,
                'calendar' => $data['calendar'] ?? $semester->calendar,
            ];
            $semester->update($updates);
            return $semester;
        });
    }

    public function deleteSemester($id)
    {
        DB::transaction(function() use ($id) {
            $semester = Semester::findOrFail($id);
            $semester->delete();
        });
    }

}

