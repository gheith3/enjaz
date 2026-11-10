<?php

namespace App\Filament\Resources\Achievers\RelationManagers;

use App\Filament\Support\Translatable;
use App\Models\AchieverJourneyItem;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** "مسيرتي": stations of the achiever's journey, in order. */
class JourneyItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'journeyItems';

    protected static ?string $title = 'محطات المسيرة';

    protected static ?string $modelLabel = 'محطة';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Translatable::text('title', 'المحطة'),
            TextInput::make('year')->label('السنة')->required()->maxLength(20),
            Select::make('icon')->label('الرمز')->options(AchieverJourneyItem::ICONS)->required()->default('star'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->paginated(false)
            ->columns([
                TextColumn::make('year')->label('السنة'),
                TextColumn::make('title')->label('المحطة'),
                TextColumn::make('icon')->label('الرمز')->formatStateUsing(fn (string $state) => AchieverJourneyItem::ICONS[$state] ?? $state),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
