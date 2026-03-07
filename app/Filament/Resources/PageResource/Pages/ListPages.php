<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPages extends ListRecords
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('editHomepage')
                ->label('Edit Homepage')
                ->icon('heroicon-o-home')
                ->url(PageResource::getUrl('homepage')),
            Actions\CreateAction::make(),
        ];
    }
}
