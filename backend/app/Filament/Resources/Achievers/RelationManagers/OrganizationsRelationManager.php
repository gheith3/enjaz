<?php

namespace App\Filament\Resources\Achievers\RelationManagers;

use App\Filament\Resources\Organizations\RelationManagers\AchieversRelationManager;
use App\Models\AchieverOrganization;
use App\Models\Organization;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** The achiever's organizations (university, federation ...): kind and role. */
class OrganizationsRelationManager extends RelationManager
{
    protected static string $relationship = 'organizations';

    protected static ?string $title = 'المؤسسات';

    protected static ?string $modelLabel = 'مؤسسة';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(AchieversRelationManager::pivotFields());
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('المؤسسة'),
                TextColumn::make('pivot.kind')->label('الصلة')->formatStateUsing(fn (?string $state) => AchieverOrganization::KINDS[$state] ?? $state)->badge(),
                TextColumn::make('role')->label('الصفة')->state(fn (Organization $record) => $record->pivot->roleLabel()),
            ])
            ->headerActions([
                AttachAction::make()->label('ربط مؤسسة')->preloadRecordSelect()->recordSelectSearchColumns(['slug'])
                    ->recordTitle(fn (Organization $record) => $record->name)
                    ->schema(fn (AttachAction $action) => [$action->getRecordSelect(), ...AchieversRelationManager::pivotFields()]),
            ])
            ->recordActions([EditAction::make(), DetachAction::make()->label('فك الربط')]);
    }
}
