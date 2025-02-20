<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AchievementResource\Pages;
use App\Filament\Resources\AchievementResource\RelationManagers;
use App\Models\Achievement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AchievementResource extends Resource
{
    protected static ?string $model = Achievement::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

   
    public static function getModelLabel(): string
    {
        return __('app.achievements.resource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.achievements.resource.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.achievements.sections.owners'))
                    ->schema([
                        Forms\Components\Select::make('organizations')
                            ->label(__('app.achievements.fields.organizations'))
                            ->multiple()
                            ->relationship('organizations', 'name_ar')
                            ->preload()
                            ->searchable(),
                        Forms\Components\Select::make('publicFigures')
                            ->label(__('app.achievements.fields.public_figures'))
                            ->multiple()
                            ->relationship('publicFigures', 'name_ar')
                            ->preload()
                            ->searchable(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make(__('app.achievements.sections.categories'))
                    ->schema([
                        Forms\Components\Select::make('achievement_scope_id')
                            ->label(__('app.achievements.fields.achievement_scope'))
                            ->relationship('achievementScope', 'title_en')
                            ->required(),
                        Forms\Components\Select::make('achievement_main_type_id')
                            ->relationship('achievementMainType', 'title_en')
                            ->required(),
                        Forms\Components\Select::make('sub_achievement_type_id')
                            ->relationship('subAchievementType', 'title_en')
                            ->required(),
                    ])
                    ->columns(3),
                Forms\Components\Section::make(__('app.achievements.sections.arabic_details'))
                    ->schema([
                        Forms\Components\TextInput::make('title_ar')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('description_ar')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
                Forms\Components\Section::make(__('app.achievements.sections.english_details'))
                    ->schema([
                        Forms\Components\TextInput::make('title_en')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('description_en')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
                Forms\Components\Section::make(__('app.achievements.sections.media'))
                    ->schema([
                        Forms\Components\FileUpload::make('cover_image_path')
                            ->label(__('app.achievements.fields.cover_image'))
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull()
                            ->maxSize(1024 * 5)
                            ->nullable(),
                        Forms\Components\TextInput::make('video_url')
                            ->url()
                            ->columnSpanFull(),
                    ]),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('achievementScope.title_ar')
                    ->label(__('app.achievements.table.scope'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('achievementMainType.title_ar')
                    ->label(__('app.achievements.table.main_type'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subAchievementType.title_ar')
                    ->label(__('app.achievements.table.sub_type'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title_ar')
                    ->label(__('app.achievements.fields.title'))
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
            'index' => Pages\ListAchievements::route('/'),
            'create' => Pages\CreateAchievement::route('/create'),
            'view' => Pages\ViewAchievement::route('/{record}'),
            'edit' => Pages\EditAchievement::route('/{record}/edit'),
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
