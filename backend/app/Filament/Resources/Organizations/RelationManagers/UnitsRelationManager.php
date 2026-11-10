<?php

namespace App\Filament\Resources\Organizations\RelationManagers;

use App\Filament\Support\Translatable;
use App\Models\OrganizationUnit;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/** Colleges (with opening year), units, research centers and branches. */
class UnitsRelationManager extends RelationManager
{
    protected static string $relationship = 'units';

    protected static ?string $title = 'الكليات والوحدات';

    protected static ?string $modelLabel = 'وحدة';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('kind')->label('النوع')->options(OrganizationUnit::KINDS)->required()->default('college'),
            TextInput::make('year')->label('سنة الافتتاح')->numeric(),
            Translatable::text('name', 'الاسم'),
            Translatable::text('group_label', 'عنوان المجموعة (للوحدات)', required: false),
            Translatable::text('note', 'ملاحظة', required: false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->reorderable('sort_order')->defaultSort('sort_order')->paginated(false)
            ->columns([
                TextColumn::make('kind')->label('النوع')->formatStateUsing(fn (string $state) => OrganizationUnit::KINDS[$state] ?? $state)->badge(),
                TextColumn::make('year')->label('السنة'),
                TextColumn::make('name')->label('الاسم'),
            ])
            ->filters([SelectFilter::make('kind')->label('النوع')->options(OrganizationUnit::KINDS)])
            ->headerActions([CreateAction::make()])->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
