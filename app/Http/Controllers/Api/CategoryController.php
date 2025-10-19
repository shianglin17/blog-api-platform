<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);

        $categories = $this->categoryService->getCategories(
            $request->user(),
            $perPage
        );

        return ApiResponse::success(
            data: $categories,
            message: 'Categories retrieved successfully'
        );
    }
}
