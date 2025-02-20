<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AchievementScopeResource\Pages;
use App\Models\AchievementScope;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AchievementScopeResource extends Resource
{
    protected static ?string $model = AchievementScope::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('app.group.types');
    }
    
    public static function getModelLabel(): string
    {
        return __('app.achievement_scopes.resource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.achievement_scopes.resource.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.achievement_scopes.sections.arabic_details'))
                    ->schema([
                        Forms\Components\TextInput::make('title_ar')
                            ->label(__('app.achievement_scopes.fields.title'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('description_ar')
                            ->label(__('app.achievement_scopes.fields.description'))
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('app.achievement_scopes.sections.english_details'))
                    ->schema([
                        Forms\Components\TextInput::make('title_en')
                            ->label(__('app.achievement_scopes.fields.title'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('description_en')
                            ->label(__('app.achievement_scopes.fields.description'))
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\FileUpload::make('icon')
                    ->label(__('app.achievement_scopes.fields.icon'))
                    ->image()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title_ar')
                    ->label(__('app.achievement_scopes.table.title'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('app.achievement_scopes.table.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('app.achievement_scopes.table.updated_at'))
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
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListAchievementScopes::route('/'),
            // 'create' => Pages\CreateAchievementScope::route('/create'),
            // 'view' => Pages\ViewAchievementScope::route('/{record}'),
            // 'edit' => Pages\EditAchievementScope::route('/{record}/edit'),
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
