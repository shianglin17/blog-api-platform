<?php

namespace App\Swagger\Controller\Api;

use OpenApi\Attributes as OA;

class AuthController
{
    #[OA\Post(
        path: '/login',
        summary: '用戶登入',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password'),
                ]
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: '登入成功',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'access_token', type: 'string', example: '1|abc123...'),
                                new OA\Property(property: 'refresh_token', type: 'string', example: 'def456...'),
                                new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'code', type: 'string', example: '0200'),
                        new OA\Property(property: 'message', type: 'string', example: 'Login successful'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: '登入失敗',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 422,
                description: '驗證失敗',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
            ),
        ]
    )]
    public function login()
    {
    }

    #[OA\Post(
        path: '/refresh-token',
        summary: '刷新訪問 token',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['refresh_token'],
                properties: [
                    new OA\Property(property: 'refresh_token', type: 'string', example: 'def456...'),
                ]
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Token 刷新成功',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'access_token', type: 'string', example: '2|xyz789...'),
                                new OA\Property(property: 'refresh_token', type: 'string', example: 'ghi012...'),
                                new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'code', type: 'string', example: '0200'),
                        new OA\Property(property: 'message', type: 'string', example: 'Token refreshed successfully'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Refresh token 無效或過期',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
        ]
    )]
    public function refresh()
    {
    }

    #[OA\Get(
        path: '/profile',
        summary: '獲取當前用戶資料',
        security: [['bearerAuth' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: '成功獲取用戶資料',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Admin User'),
                                new OA\Property(property: 'email', type: 'string', example: 'admin@example.com'),
                                new OA\Property(property: 'role', type: 'string', example: 'admin'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'code', type: 'string', example: '0200'),
                        new OA\Property(property: 'message', type: 'string', example: 'Profile retrieved successfully'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: '未認證',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
        ]
    )]
    public function profile()
    {
    }

    #[OA\Post(
        path: '/logout',
        summary: '用戶登出',
        security: [['bearerAuth' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: '登出成功',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'code', type: 'string', example: '0200'),
                        new OA\Property(property: 'message', type: 'string', example: 'Logout successful'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: '未認證',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
        ]
    )]
    public function logout()
    {
    }
}
