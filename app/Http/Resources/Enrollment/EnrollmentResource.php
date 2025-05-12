<?php

namespace App\Http\Resources\Enrollment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->loadMissing('course.semester');
        //return parent::toArray($request);
        return [
            'id' => $this->id,
            'enrollment_date' => $this->enrollment_date,
            'course' => [
                'id' => $this->course_id,
                'schedule' => $this->course->schedule,
            ],
            'signature' => [
                'id' => $this->course->signature->signature_id,
                'name' => $this->course->signature->name,
            ],
            'semester' => [
                'id' => $this->course->semester->first()->id,
                'start_date' => $this->course->semester->first()->start_date,
                'end_date' => $this->course->semester->first()->end_date,
            ],
            'student' => [
                'id' => $this->student_id,
                'name' => $this->student->name,
            ],
            'final_grade' => $this->final_grade,
        ];
    }
}
