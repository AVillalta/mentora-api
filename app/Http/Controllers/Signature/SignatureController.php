<?php

namespace App\Http\Controllers\Signature;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Signature\SignatureStoreRequest;
use App\Http\Requests\Signature\SignatureUpdateRequest;
use App\Http\Resources\Signature\SignatureResource;
use App\Models\Signature\Signature;
use App\Services\Signature\SignatureService;
use App\Traits\ApiResponse;
use Illuminate\Http\Response;

class SignatureController extends Controller
{

    use ApiResponse;

    protected $SignatureService;

    public function __construct(SignatureService $SignatureService)
    {
        $this->SignatureService = $SignatureService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $result = $this->SignatureService->getAllSignatures();

        if (isset($result[0]) && $result[0] instanceof Signature) {
            $result = SignatureResource::collection($result);
        }

        return $this->successResponse($result, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SignatureStoreRequest $request)
    {
        $data = $request->validated();
        $result = $this->SignatureService->saveSignature($data);

        if ($result instanceof Signature) {
            $result = new SignatureResource($result);
        }

        return $this->successResponse($result, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = ['signature_id' => $id];
        $result = $this->SignatureService->showSignature($data);

        if ($result instanceof Signature) {
            $result = new SignatureResource($result);
        }

        return $this->successResponse($result, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SignatureUpdateRequest $request, $id)
    {
        $data = $request->validated();
        $result = $this->SignatureService->updateSignature($data, $id);

        if ($result instanceof Signature) {
            $result = new SignatureResource($result);
        }

        return $this->successResponse($result, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = $this->SignatureService->deleteSignature($id);

        return $this->successResponse($result, Response::HTTP_NO_CONTENT);
    }
}
