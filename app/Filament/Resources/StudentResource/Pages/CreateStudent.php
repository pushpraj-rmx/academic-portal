<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Services\StudentCreationService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        /** @var StudentCreationService $service */
        $service = app(StudentCreationService::class);

        return $service->createFromArray($data);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Student created successfully';
    }

    protected function afterCreate(): void
    {
        // Ensure a visible success message (useful when default Filament "Created" toast is missed).
        Notification::make()
            ->title('Student created successfully')
            ->success()
            ->send();
    }
}
