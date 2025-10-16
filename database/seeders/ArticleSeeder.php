<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $frontend = Category::where('slug', 'frontend')->first();
        $frontendArticles = [
            [
                'slug' => 'vue3-composition-api',
                'title' => 'Vue 3 Composition API 完整指南',
                'content' => 'Vue 3 的 Composition API 是一個全新的組合式 API，提供更好的邏輯複用和代碼組織方式...',
                'description' => '深入介紹 Vue 3 Composition API 的使用方法與最佳實踐'
            ],
            [
                'slug' => 'react-hooks-tutorial',
                'title' => 'React Hooks 從入門到精通',
                'content' => 'React Hooks 讓我們可以在函數組件中使用 state 和其他 React 特性...',
                'description' => '完整解析 React Hooks 的核心概念與實戰應用'
            ],
            [
                'slug' => 'javascript-es2024',
                'title' => 'JavaScript ES2024 新特性總覽',
                'content' => 'ES2024 帶來了許多令人興奮的新特性，包括新的陣列方法、裝飾器等...',
                'description' => '探索 JavaScript 最新版本的所有新功能與改進'
            ],
        ];

        foreach ($frontendArticles as $article) {
            Article::create(array_merge($article, ['category_id' => $frontend->id]));
        }

        // 後端分類文章
        $backend = Category::where('slug', 'backend')->first();
        $backendArticles = [
            [
                'slug' => 'laravel-best-practices',
                'title' => 'Laravel 開發最佳實踐',
                'content' => 'Laravel 是一個優雅的 PHP 框架，遵循最佳實踐可以讓你的專案更易維護...',
                'description' => '整理 Laravel 開發中的最佳實踐與常見模式'
            ],
            [
                'slug' => 'php-performance-optimization',
                'title' => 'PHP 效能優化技巧',
                'content' => 'PHP 效能優化包含多個層面，從代碼層級到伺服器配置都需要注意...',
                'description' => '提升 PHP 應用程式效能的實用技巧與方法'
            ],
            [
                'slug' => 'database-design-principles',
                'title' => '資料庫設計原則與正規化',
                'content' => '良好的資料庫設計是系統穩定運行的基礎，正規化是重要的設計原則...',
                'description' => '深入理解資料庫設計的核心原則與正規化理論'
            ],
        ];

        foreach ($backendArticles as $article) {
            Article::create(array_merge($article, ['category_id' => $backend->id]));
        }

        // 生活分類文章
        $life = Category::where('slug', 'life')->first();
        $lifeArticles = [
            [
                'slug' => 'work-life-balance',
                'title' => '工程師的工作與生活平衡',
                'content' => '在高壓的科技業中，如何保持工作與生活的平衡是每個工程師都需要面對的課題...',
                'description' => '分享工程師如何在工作與生活之間找到平衡點'
            ],
            [
                'slug' => 'remote-work-experience',
                'title' => '遠端工作一年心得分享',
                'content' => '遠端工作已經成為新常態，這一年的遠端工作經驗讓我有很多感觸...',
                'description' => '記錄一年遠端工作的經驗、挑戰與收穫'
            ],
            [
                'slug' => 'tokyo-travel-guide',
                'title' => '東京自由行完整攻略',
                'content' => '東京是一個充滿魅力的城市，這篇文章整理了我的東京自由行經驗...',
                'description' => '東京旅遊景點推薦、交通資訊與美食介紹'
            ],
        ];

        foreach ($lifeArticles as $article) {
            Article::create(array_merge($article, ['category_id' => $life->id]));
        }
    }
}
