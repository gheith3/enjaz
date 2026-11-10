<?php

namespace App\Filament\Resources\Redirects\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RedirectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('hits', 'desc')
            ->columns([
                TextColumn::make('from_path')->label('من')->searchable()->extraAttributes(['dir' => 'ltr']),
                TextColumn::make('to_path')->label('إلى')->searchable()->extraAttributes(['dir' => 'ltr']),
                TextColumn::make('status_code')->label('النوع')->badge(),
                TextColumn::make('hits')->label('الزيارات')->numeric()->sortable(),
                TextColumn::make('last_hit_at')->label('آخر زيارة')->since()->sortable(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
