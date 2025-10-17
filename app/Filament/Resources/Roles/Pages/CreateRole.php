<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function afterCreate(): void
    {
        $permissions = $this->data['permissions'] ?? [];

        // 使用 Spatie Permission 的 syncPermissions 方法
        $this->record->syncPermissions($permissions);
    }
}
