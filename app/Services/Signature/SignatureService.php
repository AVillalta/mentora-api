<?php

namespace App\Services\Signature;

use App\Models\Signature\Signature;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use PhpParser\Node\Expr\FuncCall;

class SignatureService{

    /**
     * get all signatures.
     *
     * @param
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Signature[]
     */
    public function getAllSignatures() {
        return Signature::all();
    }

     /**
     * Create new signature.
     *
     * @param array $data
     * @return \App\Models\Signature
     */
    public function saveSignature(array $data) {

        return DB::transaction(function() use ($data){
            $signature = Signature::create([
                'name' => $data['name'],
                'syllabus' => json_encode($data['syllabus'] ?? []),
                'professor_id' => $data['professor_id'],
            ]);

            if (isset($data['syllabus_pdf'])) {
                $signature->addMedia($data['syllabus_pdf'])
                          ->toMediaCollection('syllabus_pdf');
            }

            return $signature;
        });
    }

    /**
     * Get signature by id.
     *
     * @param $data
     * @return \App\Models\Signature
     */
    public function showSignature($data)
    {
        $result = Signature::findOrFail($data["signature_id"]);
        return $result;
    }

     /**
     * Update signature.
     *
     * @param array $data
     * @return \App\Models\Signature
     */
    public function updateSignature(array $data, $id) {

        $signature = Signature::findOrFail($id);

        return DB::transaction(function() use ($signature, $data){
            $updates =  [
                'name' => $data['name'] ?? $signature->name,
                'syllabus' => isset($data['syllabus']) ? json_encode($data['syllabus']) : $signature->syllabus,
                'professor_id' => $data['professor_id'] ?? $signature->professor_id,
            ];
            $signature->update($updates);

            if (isset($data['syllabus_pdf'])) {
                $signature->clearMediaCollection('syllabus_pdf');
                $signature->addMedia($data['syllabus_pdf'])->toMediaCollection('syllabus_pdf');
            }
            return $signature;
        });
    }

    public function deleteSignature($id)
    {
        DB::transaction(function() use ($id) {
            $signature = Signature::findOrFail($id);
            $signature->delete();
        });
    }

}