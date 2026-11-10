<?php

namespace App\Support;

/**
 * Reads a translatable value stored as ['ar' => ..., 'en' => ...] (settings, JSON blobs) in the current
 * locale, falling back to the app fallback locale (ar), then to any non-empty translation.
 */
class Localized
{
    /**
     * @param  array<string, mixed>|string|null  $value
     */
    public static function get(array|string|null $value, ?string $locale = null): string
    {
        if ($value === null) {
            return '';
        }

        if (is_string($value)) {
            return $value;
        }

        $locale ??= app()->getLocale();

        foreach ([$locale, config('app.fallback_locale')] as $candidate) {
            if (filled($value[$candidate] ?? null)) {
                return (string) $value[$candidate];
            }
        }

        foreach ($value as $translation) {
            if (filled($translation)) {
                return (string) $translation;
            }
        }

        return '';
    }
}
