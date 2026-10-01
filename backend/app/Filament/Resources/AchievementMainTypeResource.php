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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;

class AchievementMainTypeResource extends Resource
{

    protected static ?string $model = AchievementMainType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('app.group.types');
    }

    public static function getModelLabel(): string
    {
        return __('app.achievement_main_types.resource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.achievement_main_types.resource.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.achievement_main_types.resource.nav_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('')
                    ->schema([
                        Forms\Components\TextInput::make('title_ar')
                            ->label(__('app.title_ar'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('title_en')
                            ->label(__('app.title_en'))
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title_ar')
                    ->label(__('app.title_ar'))
                    ->searchable(),
                TextColumn::make('title_en')
                    ->label(__('app.title_en'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('app.achievement_main_types.fields.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('app.achievement_main_types.actions.edit')),
                Tables\Actions\DeleteAction::make()
                    ->label(__('app.achievement_main_types.actions.delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            // 'create' => Pages\CreateAchievementMainType::route('/create'),
            // 'edit' => Pages\EditAchievementMainType::route('/{record}/edit'),
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
