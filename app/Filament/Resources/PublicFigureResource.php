<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PublicFigureResource\Pages;
use App\Filament\Resources\PublicFigureResource\RelationManagers;
use App\Models\PublicFigure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PublicFigureResource extends Resource
{
    protected static ?string $model = PublicFigure::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Public Figure Information')
                    ->schema([
                        Forms\Components\TextInput::make('name_ar')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name_en')
                            ->maxLength(255)
                            ->default(null),
                    ])->columns(2),
                Forms\Components\Section::make('Public Figure Description')
                    ->schema([
                        Forms\Components\RichEditor::make('description_ar')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('description_en')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Public Figure Description')
                    ->schema([
                        Forms\Components\FileUpload::make('image_path')
                            ->label('image')
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
