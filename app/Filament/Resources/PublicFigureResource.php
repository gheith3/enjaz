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
                Forms\Components\Section::make(__('app.public_figures.sections.arabic_details'))
                    ->schema([
                        Forms\Components\TextInput::make('name_ar')
                            ->label(__('app.public_figures.fields.name_ar'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('position_ar')
                            ->label(__('app.public_figures.fields.position_ar'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('description_ar')
                            ->label(__('app.public_figures.fields.description_ar'))
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('app.public_figures.sections.english_details'))
                    ->schema([
                        Forms\Components\TextInput::make('name_en')
                            ->label(__('app.public_figures.fields.name_en'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('position_en')
                            ->label(__('app.public_figures.fields.position_en'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('description_en')
                            ->label(__('app.public_figures.fields.description_en'))
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('app.public_figures.sections.media'))
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label(__('app.public_figures.fields.image'))
                            ->image()
                            ->imageEditor()
                            ->required(),
                    ])
                    ->columns(1),

                Forms\Components\Section::make(__('app.public_figures.sections.basic_details'))
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label(__('app.public_figures.fields.email'))
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('app.public_figures.fields.phone'))
                            ->tel()
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label(__('app.public_figures.fields.status'))
                            ->options([
                                'active' => __('app.public_figures.statuses.active'),
                                'inactive' => __('app.public_figures.statuses.inactive'),
                            ])
                            ->required(),
                    ])
                    ->columns(3),
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
