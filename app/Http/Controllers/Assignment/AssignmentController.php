<?php

namespace App\Http\Controllers\Assignment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Assignment\AssignmentStoreRequest;
use App\Http\Requests\Assignment\AssignmentUpdateRequest;
use App\Http\Resources\Assignment\AssignmentResource;
use App\Models\Assignment\Assignment;
use App\Services\Assignment\AssignmentService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AssignmentController extends Controller
{
    use ApiResponse;

    protected $assignmentService;

    public function __construct(AssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    public function index()
    {
        $result = $this->assignmentService->getAllAssignments();

        if (isset($result[0]) && $result[0] instanceof Assignment) {
            $result = AssignmentResource::collection($result);
        }

        return $this->successResponse($result, Response::HTTP_OK);
    }

    public function store(AssignmentStoreRequest $request)
    {
        $data = $request->validated();
        $result = $this->assignmentService->saveAssignment($data);

        if ($result instanceof Assignment) {
            $result = new AssignmentResource($result);
        }

        return $this->successResponse($result, Response::HTTP_CREATED);
    }

    public function submit(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $assignment = Assignment::findOrFail($id);
        $student = $request->user();

        $result = $this->assignmentService->submitAssignment($assignment, $request->file('file'), $student);

        return $this->successResponse($result, Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $data = ['assignment_id' => $id];
        $result = $this->assignmentService->showAssignment($data);

        if ($result instanceof Assignment) {
            $result = new AssignmentResource($result);
        }

        return $this->successResponse($result, Response::HTTP_OK);
    }

    public function update(AssignmentUpdateRequest $request, $id)
    {
        $data = $request->validated();
        $result = $this->assignmentService->updateAssignment($data, $id);

        if ($result instanceof Assignment) {
            $result = new AssignmentResource($result);
        }

        return $this->successResponse($result, Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $result = $this->assignmentService->deleteAssignment($id);

        return $this->successResponse($result, Response::HTTP_NO_CONTENT);
    }
}