<?php

namespace App\Http\Resources\Grade;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeResource extends JsonResource
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
            'grade_type' => $this->grade_type,
            'grade_value' => $this->grade_value,
            'grade_date' => $this->grade_date->format('Y-m-d'), 
            'enrollment_id' => $this->enrollment_id,
        ];
    }
}
