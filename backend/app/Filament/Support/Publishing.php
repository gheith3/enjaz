<?php

namespace App\Filament\Support;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Toggle;

/**
 * Draft / publish fields shared by content resources (model uses App\Models\Concerns\HasPublishing).
 */
class Publishing
{
    /** @return list<Field> */
    public static function fields(): array
    {
        return [
            Toggle::make('is_published')->label('منشور')->helperText('غير المنشور يظهر في لوحة التحكم فقط.'),
            DateTimePicker::make('published_at')->label('موعد النشر (اختياري)')->native(false)
                ->helperText('للنشر لاحقًا. فارغ = فورًا عند تفعيل «منشور».'),
        ];
    }
}
