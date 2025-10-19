<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryService
{
    public function getCategories(User $user, int $perPage = 15): LengthAwarePaginator
    {
        $accessibleSlugs = $this->getAccessibleArticleSlugs($user);

        if (empty($accessibleSlugs)) {
            return Category::where('id', '<', 0)->paginate($perPage);
        }

        return Category::whereHas('articles', fn ($query) => $query->whereIn('slug', $accessibleSlugs))
            ->withCount([
                'articles as accessible_articles_count' => fn($query) => $query->whereIn('slug', $accessibleSlugs)
            ])
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
