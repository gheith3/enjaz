<?php

namespace App\Filament\Resources\Pages\Tables;

use App\Models\Page;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id')
            ->columns([
                TextColumn::make('title')->label('الصفحة')->searchable(),
                TextColumn::make('key')->label('المعرّف')->badge()->color('gray'),
                TextColumn::make('sections_count')->counts('sections')->label('الأقسام'),
                IconColumn::make('noindex')->label('مخفية عن البحث')->boolean(),
                TextColumn::make('updated_at')->label('آخر تعديل')->since(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('open')->label('عرض في الموقع')->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Page $record) => $record->publicUrl(), shouldOpenInNewTab: true)
                    ->visible(fn (Page $record) => $record->publicUrl() !== null),
            ]);
    }
}
