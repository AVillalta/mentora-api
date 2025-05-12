<?php

namespace App\Http\Resources\Course;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
            'schedule' => $this->schedule,
            'weighting' => $this->weighting,
            'signature' => $this->signature?->name,
            'semester' => $this->semester?->name,
            'professor' => $this->signature?->professor?->name,
            'enrollments_count' => $this->enrollments->count(),
        ];
    }
}
