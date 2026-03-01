<?php

namespace App\Filament\Resources\DownloadableFormResource\Pages;

use App\Filament\Resources\DownloadableFormResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDownloadableForms extends ListRecords
{
    protected static string $resource = DownloadableFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
