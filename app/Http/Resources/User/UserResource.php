<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'document' => $this->document,
            'country' => $this->country?->name,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'address' => $this->address,
            'date_of_birth' => $this->date_of_birth,
            'role' => $this->roles->pluck('name')->first(),
            'permissions' => $this->getAllPermissions()->pluck('name')->toArray(),
            'profile_photo_url' => $this->profile_photo_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}