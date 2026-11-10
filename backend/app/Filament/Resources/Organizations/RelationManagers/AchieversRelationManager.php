<?php

namespace App\Filament\Resources\Organizations\RelationManagers;

use App\Models\Achiever;
use App\Models\AchieverOrganization;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** People of the organization: staff, alumni, students, members. Their achievements show on its page. */
class AchieversRelationManager extends RelationManager
{
    protected static string $relationship = 'achievers';

    protected static ?string $title = 'المنتسبون';

    protected static ?string $modelLabel = 'منجز';

    public function isReadOnly(): bool
    {
        return false;
    }

    /** @return list<Field> */
    public static function pivotFields(): array
    {
        return [
            Select::make('kind')->label('الصلة')->options(AchieverOrganization::KINDS)->required()->default('member'),
            TextInput::make('role.ar')->label('الصفة (مثل: أستاذة مشاركة في قسم اللغة العربية)'),
            TextInput::make('role.en')->label('الصفة (English)')->extraInputAttributes(['dir' => 'ltr']),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(self::pivotFields());
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('achiever_organization.sort_order')
            ->columns([
                TextColumn::make('name')->label('الاسم'),
                TextColumn::make('pivot.kind')->label('الصلة')->formatStateUsing(fn (?string $state) => AchieverOrganization::KINDS[$state] ?? $state)->badge(),
                TextColumn::make('role')->label('الصفة')->state(fn (Achiever $record) => $record->pivot->roleLabel()),
            ])
            ->headerActions([
                AttachAction::make()->label('ربط منجز')->preloadRecordSelect()->recordSelectSearchColumns(['slug'])
                    ->recordTitle(fn (Achiever $record) => $record->name)
                    ->schema(fn (AttachAction $action) => [$action->getRecordSelect(), ...self::pivotFields()]),
            ])
            ->recordActions([EditAction::make(), DetachAction::make()->label('فك الربط')]);
    }
}
