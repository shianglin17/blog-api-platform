<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = Article::all();
        foreach ($articles as $article) {
            Permission::create(['name' => "article.{$article->slug}"]);
        }

        $admin = Role::create(['name' => 'admin']);
        $normal = Role::create(['name' => 'normal']);
        $silver = Role::create(['name' => 'silver']);
        $gold = Role::create(['name' => 'gold']);

        $admin->givePermissionTo(Permission::all());

        $normal->givePermissionTo([
            'article.vue3-composition-api',
            'article.react-hooks-tutorial',
            'article.javascript-es2024',
        ]);

        $silver->givePermissionTo([
            'article.vue3-composition-api',
            'article.react-hooks-tutorial',
            'article.javascript-es2024',
            'article.laravel-best-practices',
            'article.php-performance-optimization',
            'article.database-design-principles',
        ]);

        $gold->givePermissionTo(Permission::all());
    }
}
