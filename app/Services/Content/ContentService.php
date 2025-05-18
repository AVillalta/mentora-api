<?php

namespace App\Services\Content;

use App\Models\Content\Content;
use Illuminate\Support\Facades\DB;

class ContentService
{
    public function getAllContents()
    {
        return Content::all();
    }

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

    public function showContent($data)
    {
        return Content::findOrFail($data["content_id"]);
    }

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

    public function deleteContent($id)
    {
        return DB::transaction(function () use ($id) {
            $content = Content::findOrFail($id);
            $content->clearMediaCollection('content_files');
            $content->delete();
        });
    }
}