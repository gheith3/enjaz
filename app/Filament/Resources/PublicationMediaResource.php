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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        TextInput::make('title')
                            ->label('title')
                            ->required(),
                        TextInput::make('url')
                            ->label('url')
                            ->url()
                            ->required(),
                        Textarea::make('description')
                            ->label('description')
                            ->rows(4)
                            ->columnSpanFull()
                            ->required(),
                        FileUpload::make('cover_image')
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull()
                            ->maxSize(1024 * 5)
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->required(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
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
