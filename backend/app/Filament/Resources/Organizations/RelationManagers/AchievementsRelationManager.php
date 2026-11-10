<?php

namespace App\Filament\Resources\Organizations\RelationManagers;

use App\Filament\Resources\Achievements\AchievementResource;
use App\Models\Achievement;
use Filament\Actions\Action;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** The organization's own achievements (its people's achievements show automatically). */
class AchievementsRelationManager extends RelationManager
{
    protected static string $relationship = 'achievements';

    protected static ?string $title = 'إنجازات المؤسسة';

    protected static ?string $modelLabel = 'إنجاز';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->description('إنجازات المؤسسة نفسها. إنجازات منتسبيها تظهر في صفحتها تلقائيًا.')
            ->defaultSort('achieved_at', 'desc')
            ->columns([
                TextColumn::make('title')->label('الإنجاز')->limit(70),
                TextColumn::make('achieved_at')->label('التاريخ')->state(fn (Achievement $r) => $r->dateLabel()),
                IconColumn::make('is_published')->label('منشور')->boolean(),
            ])
            ->headerActions([
                Action::make('new')->label('إنجاز جديد')->icon('heroicon-o-plus')
                    ->url(fn () => AchievementResource::getUrl('create', ['organization' => $this->getOwnerRecord()->getKey()])),
                AttachAction::make()->label('ربط إنجاز موجود')->preloadRecordSelect()->recordSelectSearchColumns(['slug'])
                    ->recordTitle(fn (Achievement $record) => $record->title),
            ])
            ->recordActions([
                Action::make('edit')->label('تعديل')->icon('heroicon-o-pencil-square')->url(fn (Achievement $r) => AchievementResource::getUrl('edit', ['record' => $r])),
                DetachAction::make()->label('فك الربط'),
            ]);
    }
}
