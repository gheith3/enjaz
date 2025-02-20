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
                Forms\Components\Section::make(__('app.landing_images.sections.basic_details'))
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('app.landing_images.fields.title'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('position')
                            ->label(__('app.landing_images.fields.position'))
                            ->options([
                                'header' => __('app.landing_images.positions.header'),
                                'about' => __('app.landing_images.positions.about'),
                                'services' => __('app.landing_images.positions.services'),
                                'footer' => __('app.landing_images.positions.footer'),
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label(__('app.landing_images.fields.status'))
                            ->options([
                                'draft' => __('app.landing_images.statuses.draft'),
                                'published' => __('app.landing_images.statuses.published'),
                                'archived' => __('app.landing_images.statuses.archived'),
                            ])
                            ->required(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make(__('app.landing_images.sections.media'))
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label(__('app.landing_images.fields.image'))
                            ->image()
                            ->imageEditor()
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('alt_text')
                            ->label(__('app.landing_images.fields.alt_text'))
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('app.landing_images.sections.content'))
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label(__('app.landing_images.fields.description'))
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
                    ->label(__('app.landing_images.table.title'))
                    ->searchable(),
                Tables\Columns\SelectColumn::make('position')
                    ->label(__('app.landing_images.table.position'))
                    ->options([
                        'header' => __('app.landing_images.positions.header'),
                        'about' => __('app.landing_images.positions.about'),
                        'services' => __('app.landing_images.positions.services'),
                        'footer' => __('app.landing_images.positions.footer'),
                    ]),
                Tables\Columns\SelectColumn::make('status')
                    ->label(__('app.landing_images.table.status'))
                    ->options([
                        'draft' => __('app.landing_images.statuses.draft'),
                        'published' => __('app.landing_images.statuses.published'),
                        'archived' => __('app.landing_images.statuses.archived'),
                    ]),
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
