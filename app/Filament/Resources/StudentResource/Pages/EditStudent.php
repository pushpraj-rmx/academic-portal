<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\Student;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Prefill User-related fields (name/email) into the Student edit form.
     *
     * Filament only hydrates values from `students.*` by default, but our form includes
     * `name` and `email` which live on the related `users` table.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Student $student */
        $student = $this->getRecord();

        $student->loadMissing('user');

        if ($student->user) {
            $data['name'] = $student->user->name;
            $data['email'] = $student->user->email;
        }

        return $data;
    }

    /**
     * Update both Student and the related User record.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Student $record */
        $record->loadMissing('user');

        if ($record->user) {
            $record->user->name = $data['name'] ?? $record->user->name;
            $record->user->email = $data['email'] ?? $record->user->email;

            if (filled($data['password'] ?? null)) {
                $record->user->password = $data['password'];
            }

            $record->user->save();
        }

        unset($data['name'], $data['email'], $data['password']);

        $record->update($data);

        return $record;
    }
}
