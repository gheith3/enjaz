<?php

namespace App\Filament\Support;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

/**
 * One image slot of a record (a media library collection) with its focal point (CSS object-position,
 * e.g. "50% 20%": which part stays visible when the design crops the image) and alt text.
 * Focal point and alt are stored as custom properties of the media item.
 */
class MediaSlot
{
    public static function make(string $collection, string $label, ?string $helper = null, bool $focal = true, bool $required = false): Group
    {
        return Group::make([
            SpatieMediaLibraryFileUpload::make($collection)->label($label)->helperText($helper)
                ->collection($collection)->image()->imageEditor()->required($required)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])->maxSize(10240),
            Grid::make(2)->schema(array_filter([
                $focal ? self::property($collection, 'focal', 'نقطة التركيز', 'مثل 50% 20% (أفقي ثم عمودي). اتركه فارغًا للوسط.') : null,
                self::property($collection, 'alt', 'وصف الصورة (لقارئات الشاشة)'),
            ])),
        ])->columnSpanFull();
    }

    private static function property(string $collection, string $property, string $label, ?string $helper = null): TextInput
    {
        return TextInput::make("media_meta.$collection.$property")->label($label)->helperText($helper)->maxLength(255)
            ->extraInputAttributes($property === 'focal' ? ['dir' => 'ltr'] : [])
            ->rule($property === 'focal' ? 'regex:/^\d{1,3}%\s+\d{1,3}%$/' : 'string')
            ->dehydrated(false)
            ->afterStateHydrated(function (TextInput $component, ?Model $record) use ($collection, $property) {
                if ($record instanceof HasMedia) {
                    $component->state($record->getFirstMedia($collection)?->getCustomProperty($property));
                }
            })
            ->saveRelationshipsUsing(function (?string $state, Model $record) use ($collection, $property) {
                $media = $record instanceof HasMedia ? $record->fresh()->getFirstMedia($collection) : null;
                if ($media) {
                    filled($state) ? $media->setCustomProperty($property, trim($state)) : $media->forgetCustomProperty($property);
                    $media->save();
                }
            });
    }
}
