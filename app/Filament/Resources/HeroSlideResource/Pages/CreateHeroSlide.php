<?php

namespace App\Filament\Resources\HeroSlideResource\Pages;

use App\Filament\Resources\HeroSlideResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHeroSlide extends CreateRecord
{
    use NormalizesHeroSlideFileUploads;

    protected static string $resource = HeroSlideResource::class;

    protected function beforeValidate(): void
    {
        $this->normalizeInvalidLivewireFilePaths();
    }

    public function create(bool $another = false): void
    {
        $this->normalizeInvalidLivewireFilePaths();

        parent::create($another);
    }
}
