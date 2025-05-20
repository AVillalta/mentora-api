<?php

namespace App\Http\Controllers\User;

use App\Traits\ApiResponse;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\User\UserResource;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use App\Models\User\User;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
    use ApiResponse;

    protected $UserService;

    public function __construct(UserService $UserService)
    {
        $this->UserService = $UserService;
    }

    public function index()
    {
        $result = $this->UserService->getAllUsers();

        if (isset($result[0]) && $result[0] instanceof User) {
            $result = UserResource::collection($result); 
        }

        return $this->successResponse($result, Response::HTTP_OK);
    }

    public function show($id)
    {
        $data = ["user_id" => $id];
        
        $result = $this->UserService->showUser($data);

        if ($result instanceof User)
        { $result = new UserResource($result); }

        return $this->successResponse($result, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\\User\UserStoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserStoreRequest $request)
    {
        $data = $request->validated();

        $result = $this->UserService->saveUser($data);

        if ($result instanceof User)
        { $result = new UserResource($result); }

        return $this->successResponse($result, Response::HTTP_CREATED);
    }


    public function update( UserUpdateRequest $request, $id)
    {

        $data = $request->validated();

        $result = $this->UserService->updateUser($data, $id);

        if ($result instanceof User)
        { $result = new UserResource($result); }

        return $this->successResponse($result, Response::HTTP_OK);
        
    }

    public function destroy($id)
    {
        $result = $this->UserService->deleteUser($id);

        return $this->successResponse($result, Response::HTTP_NO_CONTENT);
    }
}
