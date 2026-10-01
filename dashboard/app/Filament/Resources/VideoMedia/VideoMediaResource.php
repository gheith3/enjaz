<?php

declare(strict_types=1);

namespace App\Filament\Resources\VideoMedia;

use App\Filament\Resources\VideoMedia\Pages\CreateVideoMedia;
use App\Filament\Resources\VideoMedia\Pages\EditVideoMedia;
use App\Filament\Resources\VideoMedia\Pages\ListVideoMedia;
use App\Filament\Resources\VideoMedia\Pages\ViewVideoMedia;
use App\Filament\Resources\VideoMedia\Schemas\VideoMediaForm;
use App\Filament\Resources\VideoMedia\Schemas\VideoMediaInfolist;
use App\Filament\Resources\VideoMedia\Tables\VideoMediaTable;
use App\Models\VideoMedia;
use BackedEnum;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VideoMediaResource extends Resource
{
    protected static ?string $model = VideoMedia::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedVideoCamera;

    public static function getNavigationGroup(): ?string
    {
        return __('common.nav_groups.media');
    }

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'title_ar';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }

    public static function form(Schema $schema): Schema
    {
        return VideoMediaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VideoMediaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VideoMediaTable::configure($table);
    }

    /**
     * @return array<class-string>
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListVideoMedia::route('/'),
            'create' => CreateVideoMedia::route('/create'),
            'view' => ViewVideoMedia::route('/{record}'),
            'edit' => EditVideoMedia::route('/{record}/edit'),
        ];
    }

    /**
     * @return array<string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['title_ar', 'title_en'];
    }

    public static function getModelLabel(): string
    {
        return __('video_media.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('video_media.model_label_plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('video_media.model_label_plural');
    }
}
