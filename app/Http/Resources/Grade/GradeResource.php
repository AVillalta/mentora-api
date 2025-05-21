<?php

namespace App\Http\Resources\Grade;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Cargar relaciones necesarias, excluyendo semester
        $this->loadMissing([
            'enrollment',
            'enrollment.student',
            'enrollment.course',
            'enrollment.course.signature',
            'enrollment.course.signature.professor',
        ]);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'grade_type' => $this->grade_type,
            'grade_value' => $this->grade_value,
            'grade_date' => $this->grade_date->format('Y-m-d'),
            'enrollment_id' => $this->enrollment_id,
            'student_name' => $this->enrollment && $this->enrollment->student ? $this->enrollment->student->name : 'Estudiante desconocido',
            'student_email' => $this->enrollment && $this->enrollment->student ? $this->enrollment->student->email : null,
            'course_name' => $this->enrollment && $this->enrollment->course && $this->enrollment->course->signature 
                ? $this->enrollment->course->signature->name 
                : 'Curso desconocido',
            'professor_name' => $this->enrollment && $this->enrollment->course && $this->enrollment->course->signature && $this->enrollment->course->signature->professor 
                ? $this->enrollment->course->signature->professor->name 
                : 'Sin profesor',
        ];
    }
}