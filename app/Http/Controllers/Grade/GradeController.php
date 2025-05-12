<?php

namespace App\Http\Controllers\Grade;

use App\Http\Controllers\Controller;
use App\Http\Requests\Grade\GradeStoreRequest;
use App\Http\Requests\Grade\GradeUpdateRequest;
use App\Http\Resources\Grade\GradeResource;
use App\Models\Grade\Grade;
use App\Services\Grade\GradeService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GradeController extends Controller
{
    use ApiResponse;

    protected $GradeService;

    public function __construct(GradeService $gradeService)
    {
        $this->GradeService = $gradeService;
    } 
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $result = $this->GradeService->getAllGrades();

        if (isset($result[0]) && $result[0] instanceof Grade) {
            $result = GradeResource::collection($result);
        }

        return $this->successResponse($result, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GradeStoreRequest $request)
    {
        $data = $request->validated();
        $result = $this->GradeService->saveGrade($data);

        if ($result instanceof Grade) {
            $result = new GradeResource($result);
        }

        return $this->successResponse($result, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = ['grade_id' => $id];
        $result = $this->GradeService->showGrade($data);

        if ($result instanceof Grade) {
            $result = new GradeResource($result);
        }

        return $this->successResponse($result, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GradeUpdateRequest $request, $id)
    {
        $data = $request->validated();
        $result = $this->GradeService->updateGrade($data, $id);

        if ($result instanceof Grade) {
            $result = new GradeResource($result);
        }

        return $this->successResponse($result, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = $this->GradeService->deleteGrade($id);

        return $this->successResponse($result, Response::HTTP_NO_CONTENT);
    }
}
