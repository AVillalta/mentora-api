<?php

namespace App\Http\Resources\Signature;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SignatureResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $this->loadMissing(['professor', 'courses']);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'syllabus' => $this->syllabus ?? null,
            'syllabus_pdf_url' => $this->syllabus_pdf
                ? (str_starts_with($this->syllabus_pdf, 'http')
                    ? $this->syllabus_pdf
                    : url('storage/' . $this->syllabus_pdf))
                : null,
            'professor_id' => $this->professor_id,
            'professor_name' => $this->whenLoaded('professor', fn () => $this->professor?->name),
            'courses_count' => $this->whenLoaded('courses', fn () => $this->courses->count(), $this->courses_count ?? 0),
        ];
    }
}