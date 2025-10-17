<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['permissions'] = $this->record->permissions()->pluck('name')->toArray();

        return $data;
    }

    protected function afterSave(): void
    {
        $permissions = $this->data['permissions'] ?? [];

        // 使用 Spatie Permission 的 syncPermissions 方法
        $this->record->syncPermissions($permissions);
    }
}
