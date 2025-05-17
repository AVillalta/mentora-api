<?php

namespace App\Services\Assignment;

use App\Models\Assignment\Assignment;
use App\Models\User\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class AssignmentService
{
    public function getAllAssignments()
    {
        return Assignment::all();
    }

    public function saveAssignment(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Assignment::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'course_id' => $data['course_id'],
                'grade_id' => $data['grade_id'] ?? null,
                'due_date' => $data['due_date'],
                'points' => $data['points'],
                'submissions' => 0,
                'total_students' => $data['total_students'],
            ]);
        });
    }

    public function submitAssignment(Assignment $assignment, UploadedFile $file, User $student)
    {
        return DB::transaction(function () use ($assignment, $file, $student) {
            $submission = $assignment->addSubmission($file, $student);
            $assignment->increment('submissions');
            return $submission;
        });
    }

    public function showAssignment($data)
    {
        return Assignment::findOrFail($data["assignment_id"]);
    }

    public function updateAssignment(array $data, $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $assignment = Assignment::findOrFail($id);

            $updates = [
                'title' => $data['title'] ?? $assignment->title,
                'description' => $data['description'] ?? $assignment->description,
                'course_id' => $data['course_id'] ?? $assignment->course_id,
                'grade_id' => $data['grade_id'] ?? $assignment->grade_id,
                'due_date' => $data['due_date'] ?? $assignment->due_date,
                'points' => $data['points'] ?? $assignment->points,
                'submissions' => $data['submissions'] ?? $assignment->submissions,
                'total_students' => $data['total_students'] ?? $assignment->total_students,
            ];

            $assignment->update($updates);
            return $assignment;
        });
    }

    public function deleteAssignment($id)
    {
        return DB::transaction(function () use ($id) {
            $assignment = Assignment::findOrFail($id);
            $assignment->clearMediaCollection('assignment_submissions');
            $assignment->delete();
        });
    }
}