<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'slug' => 'frontend',
                'name' => '前端',
                'description' => '前端開發相關技術文章，包含 JavaScript、Vue、React 等框架與工具'
            ],
            [
                'slug' => 'backend',
                'name' => '後端',
                'description' => '後端開發相關技術文章，包含 PHP、Laravel、Node.js、資料庫等技術'
            ],
            [
                'slug' => 'life',
                'name' => '生活',
                'description' => '生活隨筆、心得分享、旅遊紀錄等日常生活文章'
            ],
        ];

        foreach ($categories as $category) {
            Category::query()->create($category);
        }
    }
}
