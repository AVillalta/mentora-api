<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Http\Requests\Course\CourseStoreRequest;
use App\Http\Requests\Course\CourseUpdateRequest;
use App\Http\Resources\Course\CourseResource;
use App\Models\Course\Course;
use App\Traits\ApiResponse;
use App\Services\Course\CourseService;
use Illuminate\Http\Response;

class CourseController extends Controller
{
    use ApiResponse;

    protected $CourseService;

    public function __construct(CourseService $CourseService)
    {
        $this->CourseService = $CourseService;
    }

    public function index()
    {
        $result = $this->CourseService->getAllCourses();
        if (isset($result[0]) && $result[0] instanceof Course) {
            $result = CourseResource::collection($result);
        }
        return $this->successResponse($result, Response::HTTP_OK);
    }

    public function store(CourseStoreRequest $request)
    {
        $data = $request->validated();
        $result = $this->CourseService->saveCourse($data);
        if ($result instanceof Course) {
            $result = new CourseResource($result);
        }
        return $this->successResponse($result, Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $data = ['course_id' => $id];
        $result = $this->CourseService->showCourse($data);
        if ($result instanceof Course) {
            $result = new CourseResource($result);
        }
        return $this->successResponse($result, Response::HTTP_OK);
    }

    public function update(CourseUpdateRequest $request, $id)
    {
        $data = $request->validated();
        $result = $this->CourseService->updateCourse($data, $id);
        if ($result instanceof Course) {
            $result = new CourseResource($result);
        }
        return $this->successResponse($result, Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $result = $this->CourseService->deleteCourse($id);
        return $this->successResponse($result, Response::HTTP_NO_CONTENT);
    }
}