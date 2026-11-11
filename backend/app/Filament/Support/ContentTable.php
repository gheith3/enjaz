<?php

namespace App\Filament\Support;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

/**
 * Columns, filters and actions shared by content tables (publishing, trash, open on site).
 */
class ContentTable
{
    public static function publishedColumn(): ToggleColumn
    {
        return ToggleColumn::make('is_published')->label('منشور');
    }

    /** @return list<BaseFilter> */
    public static function filters(): array
    {
        return [
            TernaryFilter::make('is_published')->label('النشر')->trueLabel('منشور')->falseLabel('مسودة'),
            TrashedFilter::make(),
        ];
    }

    public static function openOnSite(string $route): Action
    {
        return Action::make('open')->label('عرض')->icon('heroicon-o-arrow-top-right-on-square')->color('gray')
            ->url(fn (Model $record) => method_exists($record, 'url') ? $record->url() : (Route::has($route) ? route($route, $record) : null), shouldOpenInNewTab: true)
            ->visible(fn (Model $record) => Route::has($route) && method_exists($record, 'isLive') && $record->isLive());
    }

    public static function bulkActions(): BulkActionGroup
    {
        return BulkActionGroup::make([
            BulkAction::make('publish')->label('نشر')->icon('heroicon-o-eye')
                ->action(fn (Collection $records) => $records->each->update(['is_published' => true]))->deselectRecordsAfterCompletion(),
            BulkAction::make('unpublish')->label('إلغاء النشر')->icon('heroicon-o-eye-slash')
                ->action(fn (Collection $records) => $records->each->update(['is_published' => false]))->deselectRecordsAfterCompletion(),
            DeleteBulkAction::make(),
            RestoreBulkAction::make(),
            ForceDeleteBulkAction::make(),
        ]);
    }
}
