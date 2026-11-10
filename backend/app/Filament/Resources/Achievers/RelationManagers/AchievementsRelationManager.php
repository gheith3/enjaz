<?php

namespace App\Filament\Resources\Achievers\RelationManagers;

use App\Filament\Resources\Achievements\AchievementResource;
use App\Models\Achievement;
use Filament\Actions\Action;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** The achiever's achievements. Each achievement is edited on its own page. */
class AchievementsRelationManager extends RelationManager
{
    protected static string $relationship = 'achievements';

    protected static ?string $title = 'الإنجازات';

    protected static ?string $modelLabel = 'إنجاز';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('achieved_at', 'desc')
            ->columns([
                TextColumn::make('title')->label('الإنجاز')->limit(70),
                TextColumn::make('scope.name')->label('المستوى')->badge(),
                TextColumn::make('achieved_at')->label('التاريخ')->state(fn (Achievement $record) => $record->dateLabel()),
                IconColumn::make('pivot.is_primary')->label('صاحب الإنجاز الأساسي')->boolean(),
                IconColumn::make('is_published')->label('منشور')->boolean(),
            ])
            ->headerActions([
                Action::make('new')->label('إنجاز جديد')->icon('heroicon-o-plus')
                    ->url(fn () => AchievementResource::getUrl('create', ['achiever' => $this->getOwnerRecord()->getKey()])),
                AttachAction::make()->label('ربط إنجاز موجود')->preloadRecordSelect()->recordSelectSearchColumns(['slug'])->recordTitle(fn (Achievement $record) => $record->title)
                    ->schema(fn (AttachAction $action) => [$action->getRecordSelect(), Toggle::make('is_primary')->label('صاحب الإنجاز الأساسي')->default(true)]),
            ])
            ->recordActions([
                Action::make('edit')->label('تعديل')->icon('heroicon-o-pencil-square')
                    ->url(fn (Achievement $record) => AchievementResource::getUrl('edit', ['record' => $record])),
                DetachAction::make()->label('فك الربط'),
            ]);
    }
}
