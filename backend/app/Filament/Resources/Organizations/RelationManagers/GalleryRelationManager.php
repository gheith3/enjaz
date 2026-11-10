<?php

namespace App\Filament\Resources\Organizations\RelationManagers;

use App\Filament\Support\RelationManagers\GalleryRelationManager as BaseGallery;
use App\Support\Localized;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

/** Photos and documents of the organization (library chapters for universities, eras for federations). */
class GalleryRelationManager extends BaseGallery
{
    protected static string $relationship = 'galleryMedia';

    protected static ?string $title = 'المعرض والمكتبة';

    protected function extraFields(): array
    {
        $chapters = collect($this->getOwnerRecord()->gallery_chapters ?? [])
            ->mapWithKeys(fn ($c) => [$c['key'] => Localized::get($c['label'] ?? $c['key'])])->all();

        return [
            TextInput::make('title')->label('العنوان'),
            TextInput::make('year')->label('السنة'),
            TextInput::make('note')->label('ملاحظة')->columnSpanFull(),
            Select::make('kind')->label('النوع')->options(['photo' => 'صورة', 'doc' => 'وثيقة'])->default('photo'),
            Select::make('era')->label('الحقبة')->options(['70s' => 'السبعينيات', '80s' => 'الثمانينيات', 'early' => 'البدايات', 'later' => 'الحديثة']),
            Select::make('chapter')->label('الفصل')->options($chapters),
        ];
    }
}
