<?php

namespace App\Swagger\Controller\Api;

use OpenApi\Attributes as OA;

class ArticleController
{
    #[OA\Get(
        path: '/articles',
        summary: '獲取文章列表',
        security: [['bearerAuth' => []]],
        tags: ['Articles'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 15)),
            new OA\Parameter(name: 'category', in: 'query', required: false, schema: new OA\Schema(type: 'string', example: 'frontend')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: '成功獲取文章列表',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'title', type: 'string', example: 'Vue 3 Composition API 完整指南'),
                                    new OA\Property(property: 'slug', type: 'string', example: 'frontend-1'),
                                    new OA\Property(property: 'excerpt', type: 'string', example: '深入探討 Vue 3 Composition API 的使用方式...'),
                                    new OA\Property(
                                        property: 'category',
                                        properties: [
                                            new OA\Property(property: 'id', type: 'integer', example: 1),
                                            new OA\Property(property: 'name', type: 'string', example: '前端技術'),
                                            new OA\Property(property: 'slug', type: 'string', example: 'frontend'),
                                        ],
                                        type: 'object'
                                    ),
                                ],
                                type: 'object'
                            )
                        ),
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'code', type: 'string', example: '0200'),
                        new OA\Property(property: 'message', type: 'string', example: 'Articles retrieved successfully'),
                        new OA\Property(
                            property: 'meta',
                            properties: [
                                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                                new OA\Property(property: 'from', type: 'integer', example: 1),
                                new OA\Property(property: 'last_page', type: 'integer', example: 1),
                                new OA\Property(property: 'per_page', type: 'integer', example: 15),
                                new OA\Property(property: 'to', type: 'integer', example: 3),
                                new OA\Property(property: 'total', type: 'integer', example: 3),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(
                            property: 'links',
                            properties: [
                                new OA\Property(property: 'first', type: 'string', example: 'http://localhost/api/articles?page=1'),
                                new OA\Property(property: 'last', type: 'string', example: 'http://localhost/api/articles?page=1'),
                                new OA\Property(property: 'prev', type: 'string', example: ''),
                                new OA\Property(property: 'next', type: 'string', example: ''),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: '未認證', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function index(): void
    {
    }

    #[OA\Get(
        path: '/articles/{slug}',
        summary: '獲取文章詳情',
        security: [['bearerAuth' => []]],
        tags: ['Articles'],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string', example: 'frontend-1')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: '成功獲取文章詳情',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'title', type: 'string', example: 'Vue 3 Composition API 完整指南'),
                                new OA\Property(property: 'slug', type: 'string', example: 'frontend-1'),
                                new OA\Property(property: 'content', type: 'string', example: '# Vue 3 Composition API\n\nComposition API 是 Vue 3 中最重要的新特性...'),
                                new OA\Property(property: 'excerpt', type: 'string', example: '深入探討 Vue 3 Composition API 的使用方式...'),
                                new OA\Property(
                                    property: 'category',
                                    properties: [
                                        new OA\Property(property: 'id', type: 'integer', example: 1),
                                        new OA\Property(property: 'name', type: 'string', example: '前端技術'),
                                        new OA\Property(property: 'slug', type: 'string', example: 'frontend'),
                                    ],
                                    type: 'object'
                                ),
                                new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'code', type: 'string', example: '0200'),
                        new OA\Property(property: 'message', type: 'string', example: 'Article retrieved successfully'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: '未認證', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: '無權限訪問', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: '文章不存在', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function show(): void
    {
    }
}
