<?php

namespace App\Filament\Resources\SpecializationResource\Pages;

use App\Filament\Resources\SpecializationResource;
use App\Models\Specialization;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditSpecialization extends EditRecord
{
    protected static string $resource = SpecializationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function (Specialization $record): void {
                    if ($record->syllabi()->exists()) {
                        \Filament\Notifications\Notification::make()
                            ->title('Cannot delete specialization')
                            ->body('This specialization has syllabi linked. Remove or reassign the syllabi first.')
                            ->danger()
                            ->send();
                        throw ValidationException::withMessages([
                            'specialization' => ['This specialization has syllabi linked. Remove or reassign the syllabi first.'],
                        ]);
                    }
                }),
        ];
    }
}
