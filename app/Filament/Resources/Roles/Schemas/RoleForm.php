<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Models\Category;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->label('角色名稱')
                    ->unique(ignorable: fn ($record) => $record)
                    ->maxLength(255),

                Section::make('文章權限管理')
                    ->columns([
                        'sm' => 1,
                        'lg' => 2,
                    ])
                    // 自己全寬
                    ->columnSpanFull()
                    ->schema(self::getCategorySections()),
            ]);
    }

    protected static function getCategorySections(): array
    {
        $sections = [];

        $categories = Category::with('articles')->orderBy('name')->get();

        foreach ($categories as $category) {
            $components = [];

            if ($category->articles->isNotEmpty()) {
                $articleOptions = [];
                foreach ($category->articles as $article) {
                    $permissionName = "article.$article->slug";
                    $articleOptions[$permissionName] = $article->title;
                }

                $components[] = CheckboxList::make('permissions')
                    ->hiddenLabel()
                    ->options($articleOptions)
                    ->bulkToggleable()
                    // 同名欄位 CheckboxList::make('這裏') 只會驗證各自 options 的選項
                    // 但是 filament 會把所有同名有勾選的內容送進每一個 checkboxList
                    // 所以要使用 in() 擴大驗證範圍至所有權限
                    // (否則"前端"分類的 CheckboxList 會拒絕"後端"分類選中的權限)
                    ->in(fn () => Permission::query()->pluck('name')->toArray())
                    // 防止 Filament 嘗試將 permissions 寫入 roles 表，但是 permission 只是關聯，不是欄位/屬性
                    // 權限實際存在 role_has_permissions 關聯表中，由 afterCreate/afterSave 處理
                    ->dehydrated(false);
            }

            $sections[] = Section::make($category->name)
                ->description(
                    $category->articles->isNotEmpty()
                        ? $category->description
                        : '此分類目前沒有文章'
                )
                ->schema($components);
        }

        return $sections;
    }
}
