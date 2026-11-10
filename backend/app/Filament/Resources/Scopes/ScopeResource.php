<?php

namespace App\Filament\Resources\Scopes;

use App\Filament\Resources\Scopes\Pages\ManageScopes;
use App\Filament\Support\Translatable;
use App\Models\Scope;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ScopeResource extends Resource
{
    protected static ?string $model = Scope::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAsiaAustralia;

    protected static string|UnitEnum|null $navigationGroup = 'التصنيفات';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'مستوى';

    protected static ?string $pluralModelLabel = 'مستويات الإنجاز';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Translatable::text('name', 'الاسم (مثل: دولي)'),
            Translatable::text('achievement_label', 'التسمية في صفحة الإنجاز (مثل: إنجاز دولي)', required: false),
            TextInput::make('slug')->label('المعرّف')->required()->unique(ignoreRecord: true)->alphaDash()->extraInputAttributes(['dir' => 'ltr']),
            Toggle::make('is_international')->label('يُحسب ضمن الإنجازات الدولية')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->paginated(false)
            ->columns([
                TextColumn::make('name')->label('المستوى'),
                TextColumn::make('achievement_label')->label('في صفحة الإنجاز'),
                IconColumn::make('is_international')->label('دولي')->boolean(),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageScopes::route('/')];
    }
}
