<?php

namespace App\Filament\Resources\PlacementResource\Pages;

use App\Filament\Resources\PlacementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Validation\ValidationException;

class EditPlacement extends EditRecord
{
    protected static string $resource = PlacementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            return parent::handleRecordUpdate($record, $data);
        } catch (UniqueConstraintViolationException $e) {
            if (str_contains($e->getMessage(), 'placements_student_id_recruiter_id_academic_year_unique')) {
                throw ValidationException::withMessages([
                    'academic_year' => [__('A placement for this student, recruiter and academic year already exists.')],
                ]);
            }

            throw $e;
        }
    }
}
