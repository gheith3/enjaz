<?php

namespace App\Filament\Support;

use Closure;
use Illuminate\Database\Eloquent\Builder;

/**
 * Search and sort closures for translatable (jsonb) table columns: they work on the current locale's
 * text ("name"->>'ar'), not on the whole JSON value.
 */
class TranslatableColumn
{
    public static function search(string $column): Closure
    {
        return function (Builder $query, string $search) use ($column): Builder {
            $operator = $query->getConnection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';

            return $query->where(fn (Builder $q) => $q
                ->where("{$column}->ar", $operator, "%{$search}%")
                ->orWhere("{$column}->en", $operator, "%{$search}%"));
        };
    }

    public static function sort(string $column): Closure
    {
        return fn (Builder $query, string $direction): Builder => $query->orderBy("{$column}->".app()->getLocale(), $direction);
    }
}
