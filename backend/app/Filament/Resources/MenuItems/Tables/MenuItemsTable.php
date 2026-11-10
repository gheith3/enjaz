<?php

namespace App\Filament\Resources\MenuItems\Tables;

use App\Filament\Resources\MenuItems\MenuItemResource;
use App\Filament\Support\TranslatableColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class MenuItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->defaultGroup(Group::make('location')->label('المكان')
                ->getTitleFromRecordUsing(fn ($record) => MenuItemResource::locations()[$record->location] ?? $record->location))
            ->columns([
                TextColumn::make('label')->label('النص')->searchable(query: TranslatableColumn::search('label')),
                TextColumn::make('label_short')->label('المختصر')->toggleable(),
                TextColumn::make('url')->label('الرابط')->extraAttributes(['dir' => 'ltr']),
                IconColumn::make('show_in_desktop_nav')->label('شريط الكمبيوتر')->boolean(),
                ToggleColumn::make('is_active')->label('ظاهر'),
            ])
            ->filters([
                SelectFilter::make('location')->label('المكان')->options(MenuItemResource::locations()),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
