<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AchievementMainTypeResource\Pages;
use App\Filament\Resources\AchievementMainTypeResource\RelationManagers;
use App\Models\AchievementMainType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AchievementMainTypeResource extends Resource
{
    protected static ?string $model = AchievementMainType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Forms\Components\Section::make('')
                ->schema([
                    Forms\Components\TextInput::make('title_ar')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('title_en')
                        ->required()
                        ->maxLength(255),
                ])->columns(2)
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title_ar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title_en')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('')
            ->schema([
                TextEntry::make('title_ar')
                    ->label('title arabic'),
                TextEntry::make('title_en')
                    ->label('title english'),
            ])->columns(2)
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAchievementMainTypes::route('/'),
//            'create' => Pages\CreateAchievementMainType::route('/create'),
//            'view' => Pages\ViewAchievementMainType::route('/{record}'),
//            'edit' => Pages\EditAchievementMainType::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
