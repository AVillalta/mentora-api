<?php

namespace App\Services\Signature;

use App\Models\Signature\Signature;
use Illuminate\Support\Facades\DB;

class SignatureService
{
    public function getAllSignatures()
    {
        return Signature::withCount('courses')->get();
    }

    public function saveSignature(array $data)
    {
        return DB::transaction(function () use ($data) {
            $signature = Signature::create([
                'name' => $data['name'],
                'professor_id' => $data['professor_id'] ?? null,
            ]);

            if (isset($data['syllabus_pdf']) && $data['syllabus_pdf']) {
                $signature->addMedia($data['syllabus_pdf'])->toMediaCollection('syllabus_pdf');
            }

            return $signature;
        });
    }

    public function showSignature($data)
    {
        return Signature::findOrFail($data["signature_id"]);
    }

    public function updateSignature(array $data, $id)
    {
        $signature = Signature::findOrFail($id);
        return DB::transaction(function () use ($signature, $data) {
            $updates = [
                'name' => $data['name'] ?? $signature->name,
                'professor_id' => $data['professor_id'] ?? $signature->professor_id,
            ];
            $signature->update($updates);

            if (isset($data['syllabus_pdf']) && $data['syllabus_pdf']) {
                $signature->clearMediaCollection('syllabus_pdf');
                $signature->addMedia($data['syllabus_pdf'])->toMediaCollection('syllabus_pdf');
            }

            return $signature;
        });
    }

    public function deleteSignature($id)
    {
        return DB::transaction(function () use ($id) {
            $signature = Signature::findOrFail($id);
            $signature->clearMediaCollection('syllabus_pdf');
            $signature->delete();
        });
    }
}