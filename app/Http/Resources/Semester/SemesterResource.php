<?php

namespace App\Http\Resources\Semester;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SemesterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $this->loadMissing(['courses', 'courses.enrollments']);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'start_date' => $this->start_date->toDateString(),
            'end_date' => $this->end_date->toDateString(),
            'calendar' => $this->calendar ?? [],
            'is_active' => $this->is_active,
            'courses_count' => $this->courses->count(),
            'enrollments_count' => $this->courses->sum(fn ($course) => $this->whenLoaded('courses', fn () => $course->enrollments->count(), 0)),
        ];
    }
}