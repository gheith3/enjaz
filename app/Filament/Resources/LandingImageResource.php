<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LandingImageResource\Pages;
use App\Filament\Resources\LandingImageResource\RelationManagers;
use App\Models\LandingImage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LandingImageResource extends Resource
{
    protected static ?string $model = LandingImage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Landing Image')
                    ->schema([
                        Forms\Components\Textarea::make('description_ar')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description_en')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('image_alt')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('subtitle_ar')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('subtitle_en')
                            ->maxLength(255)
                            ->default(null),

                        Forms\Components\FileUpload::make('image_path')
                            ->label('image')
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull()
                            ->maxSize(1024 * 5)
                            ->nullable(),

                        Forms\Components\Toggle::make('is_active')
                            ->required(),
                    ])
                    ->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subtitle_ar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subtitle_en')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
            'index' => Pages\ListLandingImages::route('/'),
            'create' => Pages\CreateLandingImage::route('/create'),
            'view' => Pages\ViewLandingImage::route('/{record}'),
            'edit' => Pages\EditLandingImage::route('/{record}/edit'),
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
