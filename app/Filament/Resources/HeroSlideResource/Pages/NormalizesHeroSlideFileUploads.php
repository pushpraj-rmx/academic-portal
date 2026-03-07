<?php

namespace App\Filament\Resources\HeroSlideResource\Pages;

use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait NormalizesHeroSlideFileUploads
{
    /**
     * Clear invalid Livewire temp paths that cause UnableToRetrieveMetadata (e.g. "livewire-tmp").
     * State may be a string, array (uuid => path|TemporaryUploadedFile), or TemporaryUploadedFile.
     */
    protected function normalizeInvalidLivewireFilePaths(): void
    {
        $invalidStrings = ['livewire-tmp', 'livewire-file:livewire-tmp', 'livewire-tmp/livewire-tmp'];

        if (! isset($this->data['image'])) {
            return;
        }

        $value = $this->data['image'];

        if (is_string($value)) {
            if (in_array($value, $invalidStrings, true)) {
                $this->data['image'] = null;
            }

            return;
        }

        if ($value instanceof TemporaryUploadedFile) {
            try {
                if (! $value->exists()) {
                    $this->data['image'] = null;
                }
            } catch (\Throwable) {
                $this->data['image'] = null;
            }

            return;
        }

        if (is_array($value)) {
            $cleaned = [];
            foreach ($value as $key => $item) {
                if (is_string($item) && in_array($item, $invalidStrings, true)) {
                    continue;
                }
                if ($item instanceof TemporaryUploadedFile) {
                    try {
                        if ($item->exists()) {
                            $cleaned[$key] = $item;
                        }
                    } catch (\Throwable) {
                        // skip invalid file
                    }

                    continue;
                }
                $cleaned[$key] = $item;
            }
            $this->data['image'] = $cleaned ?: null;
        }
    }
}
