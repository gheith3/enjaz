<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubAchievementTypeResource\Pages;
use App\Filament\Resources\SubAchievementTypeResource\RelationManagers;
use App\Models\SubAchievementType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubAchievementTypeResource extends Resource
{
    protected static ?string $model = SubAchievementType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('')
                    ->schema([
                        Forms\Components\Select::make('achievement_main_type_id')
                            ->relationship(name: 'achievement_main_type', titleAttribute: 'title_ar')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('title_ar')
                            ->required()
                            ->unique()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('title_en')
                            ->required()
                            ->unique()
                            ->maxLength(255),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('achievement_main_type.title_ar')
                    ->numeric()
                    ->sortable(),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubAchievementTypes::route('/'),
            'create' => Pages\CreateSubAchievementType::route('/create'),
            'view' => Pages\ViewSubAchievementType::route('/{record}'),
            'edit' => Pages\EditSubAchievementType::route('/{record}/edit'),
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
