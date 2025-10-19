<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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

        if ($data instanceof LengthAwarePaginator) {
            $response['data'] = $data->items();
            $response['meta'] = [
                'current_page' => $data->currentPage(),
                'from' => $data->firstItem(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'to' => $data->lastItem(),
                'total' => $data->total(),
            ];
            $response['links'] = [
                'first' => $data->url(1),
                'last' => $data->url($data->lastPage()),
                'prev' => $data->previousPageUrl() ?? '',
                'next' => $data->nextPageUrl() ?? '',
            ];
        }

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
