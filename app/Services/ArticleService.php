<?php

namespace App\Services;

use App\Models\Article;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ArticleService
{
    public function getArticles(User $user, ?string $categorySlug = null, int $perPage = 15): LengthAwarePaginator
    {
        $accessibleSlugs = $this->getAccessibleArticleSlugs($user);

        if (empty($accessibleSlugs)) {
            return new LengthAwarePaginator([], 0, $perPage);
        }

        return Article::whereIn('slug', $accessibleSlugs)
            ->with('category')
            ->when($categorySlug, fn($q) => $q->whereHas('category', fn($q) => $q->where('slug', $categorySlug)))
            ->orderBy('id')
            ->paginate($perPage);
    }

    public function getArticle(User $user, string $slug): Article
    {
        $article = Article::where('slug', $slug)
            ->with('category')
            ->firstOrFail();

        $hasPermission = $user->hasPermissionTo("article.{$slug}");

        if (!$hasPermission) {
            throw new AccessDeniedHttpException();
        }

        return $article;
    }

    private function getAccessibleArticleSlugs(User $user): array
    {
        return $user->getAllPermissions()
            ->filter(fn($permission) => str_starts_with($permission->name, 'article.'))
            ->map(fn($permission) => str_replace('article.', '', $permission->name))
            ->values()
            ->toArray();
    }
}
