<?php

namespace App\Filament\Resources\StatCounterResource\Pages;

use App\Filament\Resources\StatCounterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStatCounter extends EditRecord
{
    protected static string $resource = StatCounterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
