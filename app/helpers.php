<?php

if (! function_exists('normalize_upload_filename')) {
    /**
     * Normalize a filename for safe storage and Livewire temp paths.
     * Replaces spaces and other problematic characters to avoid path/serialization issues.
     */
    function normalize_upload_filename(string $filename): string
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $basename = pathinfo($filename, PATHINFO_FILENAME);

        if ($basename === '') {
            $basename = 'file';
        }

        $normalized = (string) str($basename)
            ->replaceMatches('/\s+/', '-')
            ->replaceMatches('/[^\p{L}\p{N}\-_]/u', '-')
            ->replaceMatches('/-+/', '-')
            ->trim('-')
            ->lower();

        if ($normalized === '') {
            $normalized = 'file';
        }

        return $extension !== '' ? "{$normalized}.{$extension}" : $normalized;
    }
}

if (! function_exists('settings')) {
    /**
     * Get a site setting value by key. Uses request-level cache via SiteSetting::get().
     * For footer_text, replaces :year and :name placeholders.
     */
    function settings(string $key, ?string $default = null): ?string
    {
        $value = \App\Models\SiteSetting::get($key, $default);

        if ($key === 'footer_text' && $value !== null) {
            return str_replace(
                [':year', ':name'],
                [date('Y'), config('app.name')],
                $value
            );
        }

        return $value;
    }
}

if (! function_exists('settings_array')) {
    /**
     * Get a site setting value as an array (e.g. JSON-stored footer_emails).
     *
     * @return array<int, mixed>
     */
    function settings_array(string $key): array
    {
        $value = settings($key, '[]');

        $decoded = is_string($value) ? json_decode($value, true) : $value;

        return is_array($decoded) ? $decoded : [];
    }
}
