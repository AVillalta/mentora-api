<?php

namespace App\Http\Resources\Content;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $filePath = $this->file_path;
        // Normalizar file_path eliminando URLs completas o caminos absolutos
        if ($filePath) {
            // Eliminar dominios, URLs, y caminos absolutos
            $filePath = preg_replace('#^(https?://[^/]+/storage/|/)#', '', $filePath);
            $filePath = str_replace(storage_path('app/public/'), '', $filePath);
            $filePath = ltrim($filePath, '/');
            $filePath = asset('storage/' . $filePath);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'bibliography' => $this->bibliography,
            'order' => $this->order,
            'file_path' => $filePath,
            'type' => $this->type,
            'format' => $this->format,
            'size' => $this->size ?? 0,
            'views' => $this->views ?? 0,
            'downloads' => $this->downloads ?? 0,
            'duration' => $this->duration,
            'course_id' => $this->course_id,
            'course' => $this->course && $this->course->signature ? $this->course->signature->name : null,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}