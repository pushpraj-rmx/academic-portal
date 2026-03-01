<?php

namespace App\Filament\Resources\DownloadableFormResource\Pages;

use App\Filament\Resources\DownloadableFormResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDownloadableForm extends EditRecord
{
    protected static string $resource = DownloadableFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
