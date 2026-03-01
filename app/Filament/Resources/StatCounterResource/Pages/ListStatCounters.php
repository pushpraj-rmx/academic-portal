<?php

namespace App\Filament\Resources\StatCounterResource\Pages;

use App\Filament\Resources\StatCounterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStatCounters extends ListRecords
{
    protected static string $resource = StatCounterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
