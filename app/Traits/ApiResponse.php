<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function successResponse(int $status = 200, string $message = 'Success', $data = null): JsonResponse
    {
        return response()->json([
            'success'    => true,
            'httpStatus' => $status,
            'message'    => $message,
            'data'       => $data,
        ], $status);
    }

    protected function errorResponse(int $status = 400, string $message, $error = null): JsonResponse
    {
        return response()->json([
            'success'    => false,
            'httpStatus' => $status,
            'message'    => $message,
            'error'      => $error,
        ], $status);
    }
}
