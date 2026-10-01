<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\AppUserRole;
use App\Enums\UserStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name='.urlencode($record->full_name).'&color=7F9CF5&background=EBF4FF'),

                TextColumn::make('name')
                    ->label(__('common.fields.name'))
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->email)
                    ->weight('bold'),

                TextColumn::make('full_phone_number')
                    ->label(__('common.fields.phone'))
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->iconColor('gray')
                    ->placeholder('-'),

                TextColumn::make('status')
                    ->label(__('common.fields.status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('email_verified_at')
                    ->label(__('users.table.columns.verified'))
                    ->dateTime('M d, Y')
                    ->placeholder(__('users.table.placeholders.not_verified'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('users.table.columns.joined'))
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // SelectFilter::make('role')
                //     ->label('User Type')
                //     ->options(AppUserRole::cases())
                //     ->multiple()
                //     ->preload(),

                SelectFilter::make('status')
                    ->label(__('users.filters.status'))
                    ->options(UserStatus::class)
                    ->multiple()
                    ->preload(),

                TernaryFilter::make('email_verified')
                    ->label(__('users.filters.email_verified'))
                    ->placeholder(__('users.filters.all_users'))
                    ->trueLabel(__('users.filters.verified_only'))
                    ->falseLabel(__('users.filters.unverified_only'))
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('email_verified_at'),
                        false: fn (Builder $query) => $query->whereNull('email_verified_at'),
                    ),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->recordActions([
                ViewAction::make()
                    ->iconButton(),
                EditAction::make()
                    ->iconButton(),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
