<?php

namespace App\Filament\Resources\LeaderQuotes;

use App\Filament\Resources\LeaderQuotes\Pages\ManageLeaderQuotes;
use App\Filament\Support\MediaSlot;
use App\Filament\Support\Translatable;
use App\Models\LeaderQuote;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

/** كلمات القيادة: the slides of the home hero. */
class LeaderQuoteResource extends Resource
{
    protected static ?string $model = LeaderQuote::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|UnitEnum|null $navigationGroup = 'الصفحات والقوائم';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'كلمة';

    protected static ?string $pluralModelLabel = 'كلمات القيادة';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Translatable::text('honorific', 'اللقب (حضرة صاحب الجلالة)', required: false),
            Translatable::text('name', 'الاسم'),
            Translatable::text('postnominal', 'الدعاء أو الصفة (حفظه الله ورعاه)', required: false),
            Translatable::text('lead', 'بداية الكلمة (بخط ذهبي)'),
            Translatable::textarea('body', 'بقية الكلمة', rows: 3),
            Select::make('variant')->label('شكل الشريحة')->options(LeaderQuote::VARIANTS)->required()->default('sultan'),
            MediaSlot::make('portrait', 'الصورة (PNG مفرّغ)', focal: false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')->defaultSort('sort_order')->paginated(false)
            ->columns([
                SpatieMediaLibraryImageColumn::make('portrait')->collection('portrait')->label('')->height(48),
                TextColumn::make('name')->label('الاسم'),
                TextColumn::make('lead')->label('الكلمة')->limit(50),
                ToggleColumn::make('is_active')->label('ظاهرة'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageLeaderQuotes::route('/')];
    }
}
