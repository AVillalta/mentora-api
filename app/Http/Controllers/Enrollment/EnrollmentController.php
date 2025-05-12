<?php

namespace App\Http\Controllers\Enrollment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Enrollment\EnrollmentStoreRequest;
use App\Http\Requests\Enrollment\EnrollmentUpdateRequest;
use App\Http\Resources\Enrollment\EnrollmentResource;
use App\Models\Enrollment\Enrollment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Services\Enrollment\EnrollmentService;
use Illuminate\Http\Response;

class EnrollmentController extends Controller
{

    use ApiResponse;

    protected $EnrollmentService;

    public function __construct(EnrollmentService $EnrollmentService)
    {
        $this->EnrollmentService = $EnrollmentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $result = $this->EnrollmentService->getAllEnrollments();

        if (isset($result[0]) && $result[0] instanceof Enrollment) {
            $result = EnrollmentResource::collection($result);
        }

        return $this->successResponse($result, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EnrollmentStoreRequest $request)
    {
        $data = $request->validated();
        $result = $this->EnrollmentService->saveEnrollment($data);

        if (optional($result)['example'] == false) {
            return $this->errorResponse($result['message'], $result['httpStatus']);
        }

        if ($result instanceof Enrollment) {
            $result = new EnrollmentResource($result);
        }

        //return $this->successResponse($result, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = ['enrollment_id' => $id];
        $result = $this->EnrollmentService->showEnrollment($data);

        if ($result instanceof Enrollment) {
            $result = new EnrollmentResource($result);
        }

        return $this->successResponse($result, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EnrollmentUpdateRequest $request, $id)
    {
        $data = $request->validated();
        $result = $this->EnrollmentService->updateEnrollment($data, $id);

        if ($result instanceof Enrollment) {
            $result = new EnrollmentResource($result);
        }

        return $this->successResponse($result, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = $this->EnrollmentService->deleteEnrollment($id);

        return $this->successResponse($result, Response::HTTP_NO_CONTENT);
    }
}
