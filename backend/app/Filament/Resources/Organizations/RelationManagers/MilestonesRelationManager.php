<?php

namespace App\Filament\Resources\Organizations\RelationManagers;

use App\Filament\Support\Translatable;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Dated milestones of the organization. */
class MilestonesRelationManager extends RelationManager
{
    protected static string $relationship = 'milestones';

    protected static ?string $title = 'محطات بارزة';

    protected static ?string $modelLabel = 'محطة';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('year')->label('السنة')->required()->maxLength(20),
            Translatable::text('title', 'المحطة'),
            Translatable::text('place', 'المكان', required: false),
            Translatable::textarea('text', 'تفاصيل'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->reorderable('sort_order')->defaultSort('sort_order')->paginated(false)
            ->columns([TextColumn::make('year')->label('السنة'), TextColumn::make('title')->label('المحطة')->wrap()])
            ->headerActions([CreateAction::make()])->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
