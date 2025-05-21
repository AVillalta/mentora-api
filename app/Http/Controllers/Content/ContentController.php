<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Content\ContentStoreRequest;
use App\Http\Requests\Content\ContentUpdateRequest;
use App\Http\Resources\Content\ContentResource;
use App\Models\Content\Content;
use App\Services\Content\ContentService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ContentController extends Controller
{
    use ApiResponse;

    protected $ContentService;

    public function __construct(ContentService $ContentService)
    {
        $this->ContentService = $ContentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $result = $this->ContentService->getAllContents();

        if (isset($result[0]) && $result[0] instanceof Content) {
            $result = ContentResource::collection($result);
        }

        return $this->successResponse($result, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContentStoreRequest $request)
    {
        $data = $request->validated();
        $result = $this->ContentService->saveContent($data);

        if ($result instanceof Content) {
            $result = new ContentResource($result);
        }

        return $this->successResponse($result, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = ['content_id' => $id];
        $result = $this->ContentService->showContent($data);

        if ($result instanceof Content) {
            $result = new ContentResource($result);
        }

        return $this->successResponse($result, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContentUpdateRequest $request, $id)
    {
        $data = $request->validated();
        $result = $this->ContentService->updateContent($data, $id);

        if ($result instanceof Content) {
            $result = new ContentResource($result);
        }

        return $this->successResponse($result, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = $this->ContentService->deleteContent($id);

        return $this->successResponse($result, Response::HTTP_NO_CONTENT);
    }

    public function indexByCourse($courseId)
    {
        $contents = Content::where('course_id', $courseId)->get();
        return $this->successResponse(ContentResource::collection($contents), Response::HTTP_OK);
    }
}
