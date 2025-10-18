<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Responses\ApiResponse;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $result = $this->authService->login(
            $credentials['email'],
            $credentials['password']
        );

        if (!$result) {
            return ApiResponse::error(
                message: 'Invalid email or password',
                httpStatus: 401,
                code: '0401'
            );
        }

        return ApiResponse::success(
            data: [
                'access_token' => $result['access_token'],
                'refresh_token' => $result['refresh_token'],
                'token_type' => 'Bearer',
            ],
            message: 'Login successful'
        );
    }
}
