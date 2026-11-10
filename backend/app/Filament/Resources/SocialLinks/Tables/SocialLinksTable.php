<?php

namespace App\Filament\Resources\SocialLinks\Tables;

use App\Models\SocialLink;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class SocialLinksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->paginated(false)
            ->columns([
                TextColumn::make('platform')->label('المنصة')->formatStateUsing(fn (string $state) => __('site.social.'.$state)),
                TextColumn::make('handle')->label('الحساب')->extraAttributes(['dir' => 'ltr']),
                TextColumn::make('url')->label('الرابط')->placeholder('غير متوفر')->limit(50)
                    ->url(fn (SocialLink $record) => $record->url, shouldOpenInNewTab: true)->extraAttributes(['dir' => 'ltr']),
                ToggleColumn::make('is_active')->label('ظاهر'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
