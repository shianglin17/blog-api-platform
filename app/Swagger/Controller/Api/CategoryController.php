<?php

namespace App\Swagger\Controller\Api;

use OpenApi\Attributes as OA;

class CategoryController
{
    #[OA\Get(
        path: '/categories',
        summary: '獲取分類列表',
        security: [['bearerAuth' => []]],
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 15)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: '成功獲取分類列表',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'name', type: 'string', example: '前端技術'),
                                    new OA\Property(property: 'slug', type: 'string', example: 'frontend'),
                                    new OA\Property(property: 'description', type: 'string', example: '前端開發相關文章'),
                                    new OA\Property(property: 'accessible_articles_count', type: 'integer', example: 3),
                                ],
                                type: 'object'
                            )
                        ),
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'code', type: 'string', example: '0200'),
                        new OA\Property(property: 'message', type: 'string', example: 'Categories retrieved successfully'),
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
                                new OA\Property(property: 'first', type: 'string', example: 'http://localhost/api/categories?page=1'),
                                new OA\Property(property: 'last', type: 'string', example: 'http://localhost/api/categories?page=1'),
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
        path: '/categories/{slug}',
        summary: '獲取分類詳情',
        security: [['bearerAuth' => []]],
        tags: ['Categories'],
        parameters: [
            new OA\Parameter(name: 'slug', in: 'path', required: true, schema: new OA\Schema(type: 'string', example: 'frontend')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: '成功獲取分類詳情',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: '前端技術'),
                                new OA\Property(property: 'slug', type: 'string', example: 'frontend'),
                                new OA\Property(property: 'description', type: 'string', example: '前端開發相關文章'),
                                new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                            ],
                            type: 'object'
                        ),
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'code', type: 'string', example: '0200'),
                        new OA\Property(property: 'message', type: 'string', example: 'Category retrieved successfully'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: '未認證', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: '分類不存在或無權限', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function show(): void
    {
    }
}
