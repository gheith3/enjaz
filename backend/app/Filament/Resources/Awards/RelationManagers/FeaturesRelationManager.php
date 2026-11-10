<?php

namespace App\Filament\Resources\Awards\RelationManagers;

use App\Filament\Resources\AwardFeatures\AwardFeatureResource;
use App\Models\AwardFeature;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Extra sections of the award page (special competitions, initiatives). */
class FeaturesRelationManager extends RelationManager
{
    protected static string $relationship = 'features';

    protected static ?string $title = 'أقسام إضافية';

    protected static ?string $modelLabel = 'قسم';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table->reorderable('sort_order')->defaultSort('sort_order')->paginated(false)
            ->columns([TextColumn::make('title')->label('العنوان'), TextColumn::make('chip')->label('الوسم')])
            ->headerActions([
                Action::make('create')->label('إضافة قسم')->icon('heroicon-o-plus')
                    ->url(fn () => AwardFeatureResource::getUrl('create', ['award' => $this->getOwnerRecord()->getKey()])),
            ])
            ->recordUrl(fn (AwardFeature $record) => AwardFeatureResource::getUrl('edit', ['record' => $record]))
            ->recordActions([
                Action::make('edit')->label('فتح')->icon('heroicon-o-pencil-square')->url(fn (AwardFeature $record) => AwardFeatureResource::getUrl('edit', ['record' => $record])),
                DeleteAction::make(),
            ]);
    }
}
