<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RefreshTokenRequest;
use App\Http\Responses\ApiResponse;
use App\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {
    }

    /**
     * @throws AuthenticationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $result = $this->authService->login(
            $credentials['email'],
            $credentials['password']
        );

        return ApiResponse::success(
            data: [
                'access_token' => $result['access_token'],
                'refresh_token' => $result['refresh_token'],
                'token_type' => 'Bearer',
            ],
            message: 'Login successful'
        );
    }

    /**
     * @throws AuthenticationException
     */
    public function refresh(RefreshTokenRequest $request): JsonResponse
    {
        $refreshToken = $request->validated()['refresh_token'];

        $result = $this->authService->refresh($refreshToken);

        return ApiResponse::success(
            data: [
                'access_token' => $result['access_token'],
                'refresh_token' => $result['refresh_token'],
                'token_type' => 'Bearer',
            ],
            message: 'Token refreshed successfully'
        );
    }

    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        return ApiResponse::success(
            data: [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->first()?->name,
            ],
            message: 'Profile retrieved successfully'
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return ApiResponse::success(
            message: 'Logged out successfully'
        );
    }
}
