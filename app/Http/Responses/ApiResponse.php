<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(
        mixed $data = null,
        string $message = 'success',
        int $httpStatus = 200,
        string $code = '0200'
    ): JsonResponse {
        $response = [
            'data'    => $data['data'] ?? $data,
            'success' => true,
            'code'    => $code,
            'message' => $message,
        ];

        return response()->json($response, $httpStatus);
    }

    public static function error(
        string $message = 'error',
        int $httpStatus = 500,
        string $code = '0400'
    ): JsonResponse {
        $response = [
            'success' => false,
            'code'    => $code,
            'message' => $message,
        ];

        return response()->json($response, $httpStatus);
    }
}
