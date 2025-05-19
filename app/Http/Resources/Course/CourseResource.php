<?php

namespace App\Http\Resources\Course;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $this->loadMissing(['signature', 'semester']);
        return [
            'id' => $this->id,
            'code' => $this->code,
            'schedule' => is_array($this->schedule) ? $this->schedule : [],
            'weighting' => $this->weighting,
            'signature' => $this->signature?->name,
            'semester' => $this->semester?->name,
            'professor' => $this->signature?->professor?->name,
            'enrollments_count' => $this->enrollments->count(),
            'status' => $this->semester && $this->semester->is_active ? 'active' : 'inactive',
        ];
    }
}