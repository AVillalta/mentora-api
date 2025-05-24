<?php

namespace App\Services\Content;

use App\Models\Content\Content;
use App\Models\Enrollment\Enrollment;
use Illuminate\Support\Facades\DB;

class ContentService
{
    /**
     * Get all contents, filtered by user role.
     *
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Content[]
     */
    public function getAllContents()
    {
        $user = auth()->user();

        if ($user && $user->hasRole('student')) {
            $enrolledCourseIds = Enrollment::where('student_id', $user->id)->pluck('course_id');
            return Content::whereIn('course_id', $enrolledCourseIds)->get();
        } elseif ($user && $user->hasRole('professor')) {
            return Content::whereHas('course.signature', function ($query) use ($user) {
                $query->where('professor_id', $user->id);
            })->get();
        }

        return Content::all();
    }

    /**
     * Create new content.
     *
     * @param array $data
     * @return \App\Models\Content
     */
    public function saveContent(array $data)
    {
        return DB::transaction(function () use ($data) {
            $content = Content::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'bibliography' => $data['bibliography'] ?? null,
                'order' => $data['order'],
                'type' => $data['type'],
                'format' => $data['format'],
                'views' => 0,
                'downloads' => 0,
                'duration' => $data['duration'] ?? null,
                'course_id' => $data['course_id'],
            ]);

            if (isset($data['file'])) {
                $content->addMedia($data['file'])
                        ->toMediaCollection('content_files');
            }

            return $content;
        });
    }

    /**
     * Get content by id.
     *
     * @param array $data
     * @return \App\Models\Content
     */
    public function showContent($data)
    {
        return Content::findOrFail($data["content_id"]);
    }

    /**
     * Update content.
     *
     * @param array $data
     * @param string $id
     * @return \App\Models\Content
     */
    public function updateContent(array $data, $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $content = Content::findOrFail($id);

            $updates = [
                'name' => $data['name'] ?? $content->name,
                'description' => $data['description'] ?? $content->description,
                'bibliography' => $data['bibliography'] ?? $content->bibliography,
                'order' => $data['order'] ?? $content->order,
                'type' => $data['type'] ?? $content->type,
                'format' => $data['format'] ?? $content->format,
                'views' => $data['views'] ?? $content->views,
                'downloads' => $data['downloads'] ?? $content->downloads,
                'duration' => $data['duration'] ?? $content->duration,
                'course_id' => $data['course_id'] ?? $content->course_id,
            ];

            $content->update($updates);

            if (isset($data['file'])) {
                $content->clearMediaCollection('content_files');
                $content->addMedia($data['file'])->toMediaCollection('content_files');
            }

            return $content;
        });
    }

    /**
     * Delete content.
     *
     * @param string $id
     * @return void
     */
    public function deleteContent($id)
    {
        return DB::transaction(function () use ($id) {
            $content = Content::findOrFail($id);
            $content->clearMediaCollection('content_files');
            $content->delete();
        });
    }
}