<?php

namespace App\Http\Resources\Assignment;

use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $submissions = $this->getMedia('assignment_submissions')->map(function ($media) {
            $student = User::find($media->getCustomProperty('student_id'));
            return [
                'id' => $media->id,
                'file_name' => $media->file_name,
                'url' => $media->getUrl(),
                'size' => $media->size,
                'student_id' => $media->getCustomProperty('student_id'),
                'student_name' => $student ? $student->name : 'Estudiante desconocido', // Añadir nombre del estudiante
                'created_at' => $media->created_at->toIso8601String(),
            ];
        })->toArray();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'course_id' => $this->course_id,
            'course' => $this->course && $this->course->signature ? $this->course->signature->name : null,
            'due_date' => $this->due_date->toIso8601String(),
            'points' => $this->points,
            'submissions' => count($submissions),
            'total_students' => $this->total_students,
            'submissions_files' => $submissions,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}