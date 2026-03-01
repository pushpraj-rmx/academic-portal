<?php

namespace App\Filament\Resources\GradingRuleResource\Pages;

use App\Filament\Resources\GradingRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGradingRules extends ListRecords
{
    protected static string $resource = GradingRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
