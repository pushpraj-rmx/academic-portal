<?php

namespace App\Filament\Resources\RecruiterResource\Pages;

use App\Filament\Resources\RecruiterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecruiters extends ListRecords
{
    protected static string $resource = RecruiterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
