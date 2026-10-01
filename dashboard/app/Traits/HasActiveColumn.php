<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait HasActiveColumn
{
    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    #[Scope]
    protected function disabled(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    #[Scope]
    protected function whereActiveIs(Builder $query, bool $isActive): Builder
    {
        return $query->where('is_active', $isActive);
    }
}
