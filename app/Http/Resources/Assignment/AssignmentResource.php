<?php

namespace App\Http\Resources\Assignment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'course_id' => $this->course_id,
            'course' => $this->course ? $this->course->signature : null,
            'grade_id' => $this->grade_id,
            'due_date' => $this->due_date->toIso8601String(),
            'points' => $this->points,
            'submissions' => $this->submissions,
            'total_students' => $this->total_students,
            'submissions_files' => $this->submissions,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}