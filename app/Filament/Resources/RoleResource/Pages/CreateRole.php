<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['permissions']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $permissions = $this->form->getState()['permissions'] ?? [];
        $this->record->permissions()->sync($permissions);
    }
}
