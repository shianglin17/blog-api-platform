<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    description: '部落格 API 文件',
    title: '部落格 API',
)]
#[OA\Server(
    url: 'http://localhost:8000/api',
    description: 'API Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    description: 'Enter token in format (Bearer <token>)',
    bearerFormat: 'JWT',
    scheme: 'bearer'
)]
#[OA\Tag(
    name: 'Auth',
    description: '認證相關 API'
)]
#[OA\Tag(
    name: 'Categories',
    description: '分類相關 API'
)]
#[OA\Tag(
    name: 'Articles',
    description: '文章相關 API'
)]
class OpenApi
{
}
