<?php

namespace App\Http\Controllers\Semester;

use App\Http\Controllers\Controller;
use App\Http\Requests\Semester\SemesterStoreRequest;
use App\Http\Requests\Semester\SemesterUpdateRequest;
use App\Http\Resources\Semester\SemesterResource;
use App\Traits\ApiResponse;
use Illuminate\Http\Response;
use App\Models\Semester\Semester;
use Illuminate\Http\Request;
use App\Services\Semester\SemesterService;

class SemesterController extends Controller
{

    use ApiResponse;

    protected $SemesterService;

    public function __construct(SemesterService $SemesterService)
    {
        $this->SemesterService = $SemesterService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $result = $this->SemesterService->getAllSemesters();

        if (isset($result[0]) && $result[0] instanceof Semester) {
            $result = SemesterResource::collection($result); 
        }

        return $this->successResponse($result, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SemesterStoreRequest $request)
    {
        $data = $request->validatedData();
        $result = $this->SemesterService->saveSemester($data);

        if ($result instanceof Semester) {
            $result = new SemesterResource($result);
        }

        return $this->successResponse($result, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = ['Semester_id' => $id];
        $result = $this->SemesterService->showSemester($data);

        if ($result instanceof Semester) {
            $result = new SemesterResource($result);
        }

        return $this->successResponse($result, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SemesterUpdateRequest $request, string $id)
    {
        $data = $request->validatedData();
        $result = $this->SemesterService->updateSemester($data, $id);

        if ($result instanceof Semester) {
            $result = new SemesterResource($result);
        }

        return $this->successResponse($result, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $result = $this->SemesterService->deleteSemester($id);

        return $this->successResponse($result, Response::HTTP_NO_CONTENT);
    }
}
