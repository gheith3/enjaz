<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PublicFigureResource\Pages;
use App\Filament\Resources\PublicFigureResource\RelationManagers;
use App\Models\PublicFigure;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PublicFigureResource extends Resource
{
    protected static ?string $model = PublicFigure::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';


    public static function getModelLabel(): string
    {
        return __('app.public_figures.resource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.public_figures.resource.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('')
                    ->schema([
                        Forms\Components\TextInput::make('name_ar')
                            ->label(__('app.public_figures.fields.name_ar'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name_en')
                            ->label(__('app.public_figures.fields.name_en'))
                            ->maxLength(255)
                            ->default(null),

                        Forms\Components\TextInput::make('description_ar')
                            ->label(__('app.public_figures.fields.description_ar'))
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('description_en')
                            ->label(__('app.public_figures.fields.name_en'))
                            ->columnSpanFull(),
                    ])->columns(2),
                Section::make(__('app.public_figures.sections.story'))
                    ->schema([
                        RichEditor::make('story_ar')
                            ->label(__('app.public_figures.fields.story_ar'))
                            ->required()
                            ->columnSpan('full'),
                        RichEditor::make('story_en')
                            ->label(__('app.public_figures.fields.story_en'))
                            ->columnSpan('full'),
                    ])->columns(2),
                Section::make(__('app.public_figures.fields.social_media'))
                    ->schema([
                        Forms\Components\TextInput::make('facebook_url')
                            ->label(__('app.public_figures.fields.facebook_url')),
                        Forms\Components\TextInput::make('twitter_url')
                            ->label(__('app.public_figures.fields.twitter_url')),
                        Forms\Components\TextInput::make('instagram_url')
                            ->label(__('app.public_figures.fields.instagram_url')),
                        Forms\Components\TextInput::make('linkedin_url')
                            ->label(__('app.public_figures.fields.linkedin_url')),
                        Forms\Components\TextInput::make('whatsapp_number')
                            ->label(__('app.public_figures.fields.whatsapp_number')),
                        Forms\Components\TextInput::make('email')
                            ->label(__('app.public_figures.fields.email')),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('app.public_figures.fields.phone')),
                            
                    ])->columns(2),
                Forms\Components\Section::make('')
                    ->schema([
                        Forms\Components\FileUpload::make('image_path')
                            ->label(__('app.public_figures.fields.image'))
                            ->directory('public_figures')
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull()
                            ->maxSize(1024 * 5)
                            ->nullable(),
                    ])

            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_ar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name_en')
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
            'index' => Pages\ListPublicFigures::route('/'),
            'create' => Pages\CreatePublicFigure::route('/create'),
            'view' => Pages\ViewPublicFigure::route('/{record}'),
            'edit' => Pages\EditPublicFigure::route('/{record}/edit'),
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
