<?php

namespace App\Http\Resources\Semester;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SemesterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'calendar' => $this->calendar,
            'is_active' => $this->is_active,
            'courses_count' => $this->courses_count ?? $this->courses()->count(),
            'enrollments_count' => $this->enrollments_count ?? $this->enrollments()->count(),
        ];
    }
}