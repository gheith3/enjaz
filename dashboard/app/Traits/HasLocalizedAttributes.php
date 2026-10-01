<?php

namespace App\Traits;

trait HasLocalizedAttributes
{
    public function t(string $attribute, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();

        return $this->getAttribute("{$attribute}_{$locale}")
            ?? $this->getAttribute("{$attribute}_ar");
    }
}
