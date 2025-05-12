<?php

namespace App\Http\Resources\Signature;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SignatureResource extends JsonResource
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
            'name' => $this->name,
            'syllabus' => json_decode($this->syllabus, true),
            'syllabus_pdf_url' => $this->syllabus_pdf_url,
            'professor_id' => $this->professor?->name
        ];
    }
}
