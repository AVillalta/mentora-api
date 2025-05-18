<?php

namespace App\Http\Controllers\Enrollment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Enrollment\EnrollmentStoreRequest;
use App\Http\Requests\Enrollment\EnrollmentUpdateRequest;
use App\Http\Resources\Enrollment\EnrollmentResource;
use App\Models\Enrollment\Enrollment;
use App\Services\Enrollment\EnrollmentService;
use App\Traits\ApiResponse;
use Illuminate\Http\Response;

class EnrollmentController extends Controller
{
    use ApiResponse;

    protected $enrollmentService;

    public function __construct(EnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $result = $this->enrollmentService->getAllEnrollments();
        return $this->successResponse(EnrollmentResource::collection($result), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EnrollmentStoreRequest $request)
    {
        $data = $request->validated();
        $result = $this->enrollmentService->saveEnrollment($data);

        if (is_array($result) && isset($result['message'])) {
            return $this->errorResponse($result['message'], $result['status']);
        }

        return $this->successResponse(new EnrollmentResource($result), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = ['enrollment_id' => $id];
        $result = $this->enrollmentService->showEnrollment($data);

        if (is_array($result) && isset($result['message'])) {
            return $this->errorResponse($result['message'], $result['status']);
        }

        return $this->successResponse(new EnrollmentResource($result), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EnrollmentUpdateRequest $request, $id)
    {
        $data = $request->validated();
        $result = $this->enrollmentService->updateEnrollment($data, $id);

        if (is_array($result) && isset($result['message'])) {
            return $this->errorResponse($result['message'], $result['status']);
        }

        return $this->successResponse(new EnrollmentResource($result), Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = $this->enrollmentService->deleteEnrollment($id);

        if (is_array($result) && isset($result['message'])) {
            return $this->errorResponse($result['message'], $result['status']);
        }

        return $this->successResponse(null, Response::HTTP_NO_CONTENT);
    }
}