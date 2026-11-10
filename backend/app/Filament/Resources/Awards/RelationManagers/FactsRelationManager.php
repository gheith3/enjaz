<?php

namespace App\Filament\Resources\Awards\RelationManagers;

use App\Filament\Support\Translatable;
use App\Models\Fact;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/** Goals, who can enter, path and criteria of an award; facts of a winner. */
class FactsRelationManager extends RelationManager
{
    protected static string $relationship = 'facts';

    protected static ?string $title = 'الأهداف والشروط والمعايير';

    protected static ?string $modelLabel = 'بند';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('group')->label('القائمة')->options(Fact::GROUPS)->required()->default('goals'),
            Translatable::text('label', 'العنوان', required: false),
            Translatable::textarea('value', 'النص', rows: 2),
            Translatable::text('short', 'نص مختصر (دوائر «من يحق له المشاركة»)', required: false),
            Select::make('icon')->label('الأيقونة')->options(Fact::ICONS),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->reorderable('sort_order')->defaultSort('sort_order')->paginated(false)
            ->columns([
                TextColumn::make('group')->label('القائمة')->formatStateUsing(fn (string $state) => Fact::GROUPS[$state] ?? $state)->badge(),
                TextColumn::make('label')->label('العنوان'),
                TextColumn::make('value')->label('النص')->limit(80),
            ])
            ->filters([SelectFilter::make('group')->label('القائمة')->options(Fact::GROUPS)])
            ->headerActions([CreateAction::make()])->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
