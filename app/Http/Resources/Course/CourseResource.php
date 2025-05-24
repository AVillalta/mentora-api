<?php

namespace App\Http\Resources\Course;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $this->loadMissing(['signature', 'signature.professor', 'semester', 'enrollments']);
        return [
            'id' => $this->id,
            'code' => $this->code,
            'schedule' => is_array($this->schedule) ? $this->schedule : [],
            'weighting' => $this->weighting,
            'signature' => $this->signature?->name,
            'semester' => [
                'id' => $this->semester?->id,
                'name' => $this->semester?->name,
                'is_active' => $this->semester?->is_active ?? false,
            ],
            'professor' => $this->signature?->professor?->name,
            'students' => $this->enrollments->count(),
            'status' => $this->semester && $this->semester->is_active ? 'active' : 'inactive',
        ];
    }
}