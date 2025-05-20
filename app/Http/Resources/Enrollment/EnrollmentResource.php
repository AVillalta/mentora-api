<?php

namespace App\Http\Resources\Enrollment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Cargar todas las relaciones necesarias
        $this->loadMissing(['student', 'course', 'course.signature', 'course.signature.professor', 'course.semester']);

        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'student_id' => $this->student_id,
            'student_name' => $this->student ? $this->student->name : 'Estudiante desconocido',
            'student_email' => $this->student ? $this->student->email : null, // Añadido
            'course_name' => $this->course && $this->course->signature ? $this->course->signature->name : 'Curso desconocido',
            'professor_name' => $this->course && $this->course->signature && $this->course->signature->professor 
                ? $this->course->signature->professor->name 
                : null, // Añadido
            'enrollment_date' => $this->enrollment_date ? $this->enrollment_date->toIso8601String() : null,
            'created_at' => $this->created_at->toIso8601String(),
            'final_grade' => $this->final_grade,
            'course' => $this->course ? [
                'id' => $this->course_id,
                'schedule' => $this->course->schedule,
            ] : null,
            'signature' => $this->course && $this->course->signature ? [
                'id' => $this->course->signature->signature_id,
                'name' => $this->course->signature->name,
            ] : null,
            'semester' => $this->course && $this->course->semester ? [
                'id' => $this->course->semester->id,
                'start_date' => $this->course->semester->start_date,
                'end_date' => $this->course->semester->end_date,
            ] : null,
        ];
    }
}