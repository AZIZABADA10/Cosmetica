<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait JsonResponseTrait
{
    protected function successResponse($data, string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function errorResponse(string $message, int $code): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], $code);
    }
}
