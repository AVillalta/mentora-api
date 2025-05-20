<?php

namespace App\Http\Resources\Signature;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SignatureResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'syllabus' => json_decode($this->syllabus, true),
            'syllabus_pdf_url' => $this->syllabus_pdf,
            'professor_id' => $this->professor_id,
            'professor_name' => $this->professor?->name,
            'courses_count' => $this->courses_count ?? $this->courses()->count(),
        ];
    }
}