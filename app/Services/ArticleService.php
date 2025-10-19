<?php

namespace App\Services;

use App\Models\Article;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

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

    private function getAccessibleArticleSlugs(User $user): array
    {
        return $user->getAllPermissions()
            ->filter(fn($permission) => str_starts_with($permission->name, 'article.'))
            ->map(fn($permission) => str_replace('article.', '', $permission->name))
            ->values()
            ->toArray();
    }
}
