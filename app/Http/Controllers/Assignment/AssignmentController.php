<?php

namespace App\Http\Controllers\Assignment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Assignment\AssignmentStoreRequest;
use App\Http\Requests\Assignment\AssignmentUpdateRequest;
use App\Http\Resources\Assignment\AssignmentResource;
use App\Models\Assignment\Assignment;
use App\Models\User\User;
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

        return $this->successResponse([
            'submission_id' => $result->id,
            'file_name' => $result->file_name,
            'url' => $result->getUrl(),
        ], Response::HTTP_CREATED);
    }

    public function gradeSubmission(Request $request, Assignment $assignment, string $submissionId)
    {
        $request->validate([
            'grade_value' => 'required|numeric|min:0|max:10',
            'student_id' => 'required|uuid|exists:users,id',
        ]);

        $student = User::findOrFail($request->input('student_id'));
        $result = $this->assignmentService->gradeSubmission($assignment, $submissionId, $request->grade_value, $student);

        return $this->successResponse([
            'grade_id' => $result->id,
            'grade_value' => $result->grade_value,
            'grade_date' => $result->grade_date,
        ], Response::HTTP_OK);
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