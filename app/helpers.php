<?php

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
