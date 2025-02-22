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
                Forms\Components\Section::make('')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('app.image_media.fields.title'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label(__('app.image_media.fields.description'))
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('app.image_media.fields.status'))
                            ->required(),
                    ]),
                Forms\Components\Section::make("")
                    ->schema([
                        Repeater::make('images')
                            ->label(__('app.image_media.fields.images'))
                            ->schema([
                                FileUpload::make('image')
                                    ->label(__('app.image_media.fields.image'))
                                    ->image()
                                    ->imageEditor()
                                    ->maxSize(1024 * 5)
                                    ->required()
                            ])
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
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('app.image_media.table.status'))
                    ->boolean(),
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
