<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('الاسم')->searchable(),
                TextColumn::make('email')->label('البريد')->searchable()->extraAttributes(['dir' => 'ltr']),
                TextColumn::make('roles.name')->label('الدور')->badge(),
                TextColumn::make('created_at')->label('أضيف')->since(),
            ])
            ->recordActions([EditAction::make()]);
    }
}
