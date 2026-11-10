<?php

namespace App\Filament\Resources\Organizations\RelationManagers;

use App\Filament\Support\Translatable;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Numbers on the organization hero (e.g. "+٢٠" كلية). */
class StatsRelationManager extends RelationManager
{
    protected static string $relationship = 'stats';

    protected static ?string $title = 'الأرقام';

    protected static ?string $modelLabel = 'رقم';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([Translatable::text('value', 'القيمة (مثل: +٢٠)'), Translatable::text('label', 'الوصف (مثل: كلية)')]);
    }

    public function table(Table $table): Table
    {
        return $table->reorderable('sort_order')->defaultSort('sort_order')->paginated(false)
            ->columns([TextColumn::make('value')->label('القيمة'), TextColumn::make('label')->label('الوصف')])
            ->headerActions([CreateAction::make()])->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
