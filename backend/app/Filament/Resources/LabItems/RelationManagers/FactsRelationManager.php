<?php

namespace App\Filament\Resources\LabItems\RelationManagers;

use App\Filament\Support\Translatable;
use App\Models\LabItem;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/** بطاقة الابتكار: label / value lines of an invention. */
class FactsRelationManager extends RelationManager
{
    protected static string $relationship = 'facts';

    protected static ?string $title = 'بطاقة الابتكار';

    protected static ?string $modelLabel = 'سطر';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord instanceof LabItem && $ownerRecord->isInvention();
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Translatable::text('label', 'العنوان (مثل: الميدالية)'),
            Translatable::text('value', 'القيمة'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->reorderable('sort_order')->defaultSort('sort_order')->paginated(false)
            ->columns([TextColumn::make('label')->label('العنوان'), TextColumn::make('value')->label('القيمة')->limit(80)])
            ->headerActions([CreateAction::make()->mutateDataUsing(fn (array $data) => $data + ['group' => 'facts'])])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
