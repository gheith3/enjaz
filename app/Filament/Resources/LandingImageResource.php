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

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    public static function getNavigationGroup(): ?string
    {
        return __('app.group.media');
    }

    public static function getModelLabel(): string
    {
        return __('app.landing_images.resource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.landing_images.resource.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('')
                    ->schema([
                        Forms\Components\Textarea::make('description_ar')
                            ->label(__('app.landing_images.fields.description_ar')),

                        Forms\Components\Textarea::make('description_en')
                            ->label(__('app.landing_images.fields.description_en'))
                    ])->columns(2),
                Forms\Components\Section::make('')
                    ->schema([

                        Forms\Components\TextInput::make('image_alt')
                            ->label(__('app.landing_images.fields.alt_text'))
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('subtitle_ar')
                            ->label(__('app.landing_images.fields.sub_title_ar'))
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('subtitle_en')
                            ->label(__('app.landing_images.fields.sub_title_en'))
                            ->maxLength(255)
                            ->default(null),


                        Forms\Components\FileUpload::make('image_path')
                            ->label(__('app.landing_images.fields.image'))
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull()
                            ->maxSize(1024 * 5)
                            ->nullable(),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('app.landing_images.fields.status'))
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('image_alt')
                    ->label(__('app.landing_images.fields.alt_text'))
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('app.landing_images.table.status')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('app.landing_images.table.created_at'))
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
