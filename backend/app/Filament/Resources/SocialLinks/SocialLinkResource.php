<?php

namespace App\Filament\Resources\SocialLinks;

use App\Filament\Resources\SocialLinks\Pages\CreateSocialLink;
use App\Filament\Resources\SocialLinks\Pages\EditSocialLink;
use App\Filament\Resources\SocialLinks\Pages\ListSocialLinks;
use App\Filament\Resources\SocialLinks\Schemas\SocialLinkForm;
use App\Filament\Resources\SocialLinks\Tables\SocialLinksTable;
use App\Models\SocialLink;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class SocialLinkResource extends Resource
{
    protected static ?string $model = SocialLink::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAtSymbol;

    protected static string|UnitEnum|null $navigationGroup = 'الإعدادات';

    protected static ?int $navigationSort = 20;

    protected static ?string $modelLabel = 'حساب تواصل';

    protected static ?string $pluralModelLabel = 'حسابات التواصل';

    protected static ?string $recordTitleAttribute = 'platform';

    /** Here only the site's own accounts; achievers' and writers' accounts are edited on their records. */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereNull('linkable_type');
    }

    public static function form(Schema $schema): Schema
    {
        return SocialLinkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SocialLinksTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSocialLinks::route('/'),
            'create' => CreateSocialLink::route('/create'),
            'edit' => EditSocialLink::route('/{record}/edit'),
        ];
    }
}
