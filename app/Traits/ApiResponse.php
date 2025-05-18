<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    /**
     * Return a success JSON response.
     *
     * @param mixed $data
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data, int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ], $status);
    }

    /**
     * Return an error JSON response.
     *
     * @param mixed $data
     * @param int|null $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($data, ?int $status = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data' => $data,
        ], $status ?? Response::HTTP_BAD_REQUEST);
    }
}