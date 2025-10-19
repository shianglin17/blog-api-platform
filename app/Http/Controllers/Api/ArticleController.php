<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct(
        protected ArticleService $articleService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $categorySlug = $request->input('category');

        $articles = $this->articleService->getArticles(
            $request->user(),
            $categorySlug,
            $perPage
        );

        return ApiResponse::success(
            data: $articles,
            message: 'Articles retrieved successfully'
        );
    }
}
