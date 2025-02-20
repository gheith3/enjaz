<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImageMediaResource\Pages;
use App\Filament\Resources\ImageMediaResource\RelationManagers;
use App\Models\ImageMedia;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ImageMediaResource extends Resource
{
    protected static ?string $model = ImageMedia::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';


    public static function getNavigationGroup(): ?string
    {
        return __('app.group.media');
    }

    public static function getModelLabel(): string
    {
        return __('app.image_media.resource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.image_media.resource.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.image_media.sections.basic_details'))
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('app.image_media.fields.title'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->label(__('app.image_media.fields.type'))
                            ->options([
                                'slider' => __('app.image_media.types.slider'),
                                'gallery' => __('app.image_media.types.gallery'),
                                'background' => __('app.image_media.types.background'),
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label(__('app.image_media.fields.status'))
                            ->options([
                                'draft' => __('app.image_media.statuses.draft'),
                                'published' => __('app.image_media.statuses.published'),
                                'archived' => __('app.image_media.statuses.archived'),
                            ])
                            ->required(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make(__('app.image_media.sections.media'))
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label(__('app.image_media.fields.image'))
                            ->image()
                            ->imageEditor()
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('alt_text')
                            ->label(__('app.image_media.fields.alt_text'))
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('app.image_media.sections.content'))
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label(__('app.image_media.fields.description'))
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('app.image_media.table.title'))
                    ->searchable(),
                Tables\Columns\SelectColumn::make('type')
                    ->label(__('app.image_media.table.type'))
                    ->options([
                        'slider' => __('app.image_media.types.slider'),
                        'gallery' => __('app.image_media.types.gallery'),
                        'background' => __('app.image_media.types.background'),
                    ]),
                Tables\Columns\SelectColumn::make('status')
                    ->label(__('app.image_media.table.status'))
                    ->options([
                        'draft' => __('app.image_media.statuses.draft'),
                        'published' => __('app.image_media.statuses.published'),
                        'archived' => __('app.image_media.statuses.archived'),
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('app.image_media.table.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListImageMedia::route('/'),
            'create' => Pages\CreateImageMedia::route('/create'),
            'view' => Pages\ViewImageMedia::route('/{record}'),
            'edit' => Pages\EditImageMedia::route('/{record}/edit'),
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
