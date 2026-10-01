<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PublicationMediaResource\Pages;
use App\Filament\Resources\PublicationMediaResource\RelationManagers;
use App\Models\PublicationMedia;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PublicationMediaResource extends Resource
{
    protected static ?string $model = PublicationMedia::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('app.group.media');
    }
    
    public static function getModelLabel(): string
    {
        return __('app.publication_media.resource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.publication_media.resource.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.publication_media.sections.basic_details'))
                    ->schema([
                        TextInput::make('title')
                            ->label(__('app.publication_media.fields.title'))
                            ->required(),
                        TextInput::make('url')
                            ->label(__('app.publication_media.fields.file'))
                            ->url()
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('app.publication_media.fields.status'))
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make(__('app.publication_media.sections.content'))
                    ->schema([
                        Textarea::make('description')
                            ->label(__('app.publication_media.fields.description'))
                            ->rows(4)
                            ->columnSpanFull()
                            ->required(),
                    ]),

                Forms\Components\Section::make(__('app.publication_media.sections.media'))
                    ->schema([
                        FileUpload::make('cover_image')
                            ->label(__('app.publication_media.fields.cover_image'))
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull()
                            ->maxSize(1024 * 5)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('app.publication_media.table.title'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('url')
                    ->label(__('app.publication_media.table.url'))
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('app.publication_media.table.is_active'))
                    ->boolean(),
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
            'index' => Pages\ListPublicationMedia::route('/'),
            'create' => Pages\CreatePublicationMedia::route('/create'),
            'edit' => Pages\EditPublicationMedia::route('/{record}/edit'),
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
