<?php

namespace App\Services\Semester;

use App\Models\Semester\Semester;
use Illuminate\Support\Facades\DB;

class SemesterService
{
    public function getAllSemesters()
    {
        return Semester::withCount(['courses', 'enrollments'])->get();
    }

    public function saveSemester(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Si no hay semestres activos y is_active no está especificado, marcar como activo
            if (!isset($data['is_active']) && !Semester::where('is_active', true)->exists()) {
                $data['is_active'] = true;
            }
            // Desactivar otros semestres si el nuevo es activo
            if (isset($data['is_active']) && $data['is_active']) {
                Semester::where('is_active', true)->update(['is_active' => false]);
            }
            return Semester::create([
                'name' => $data['name'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'calendar' => $data['calendar'],
                'is_active' => $data['is_active'] ?? false,
            ]);
        });
    }

    public function showSemester($data)
    {
        return Semester::findOrFail($data["Semester_id"]);
    }

    public function updateSemester(array $data, $id)
    {
        $semester = Semester::findOrFail($id);
        return DB::transaction(function () use ($semester, $data) {
            // Desactivar otros semestres si el actual se marca como activo
            if (isset($data['is_active']) && $data['is_active']) {
                Semester::where('is_active', true)->where('id', '!=', $semester->id)->update(['is_active' => false]);
            }
            $updates = [
                'name' => $data['name'] ?? $semester->name,
                'start_date' => $data['start_date'] ?? $semester->start_date,
                'end_date' => $data['end_date'] ?? $semester->end_date,
                'calendar' => $data['calendar'] ?? $semester->calendar,
                'is_active' => $data['is_active'] ?? $semester->is_active,
            ];
            $semester->update($updates);
            return $semester;
        });
    }

    public function deleteSemester($id)
    {
        return DB::transaction(function () use ($id) {
            $semester = Semester::findOrFail($id);
            $semester->delete();
        });
    }
}