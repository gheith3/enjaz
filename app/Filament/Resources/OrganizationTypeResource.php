<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizationTypeResource\Pages;
use App\Filament\Resources\OrganizationTypeResource\RelationManagers;
use App\Models\OrganizationType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrganizationTypeResource extends Resource
{
    protected static ?string $model = OrganizationType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('app.group.types');
    }
    
    public static function getModelLabel(): string
    {
        return __('app.organization_types.resource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.organization_types.resource.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.organization_types.sections.arabic_details'))
                    ->schema([
                        Forms\Components\TextInput::make('title_ar')
                            ->label(__('app.organization_types.fields.name_ar'))
                            ->required()
                            ->unique()
                            ->maxLength(255),
                    ])
                    ->columns(1),

                Forms\Components\Section::make(__('app.organization_types.sections.english_details'))
                    ->schema([
                        Forms\Components\TextInput::make('title_en')
                            ->label(__('app.organization_types.fields.name_en'))
                            ->required()
                            ->unique()
                            ->maxLength(255),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title_ar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title_en')
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
            'index' => Pages\ListOrganizationTypes::route('/'),
            'create' => Pages\CreateOrganizationType::route('/create'),
            'view' => Pages\ViewOrganizationType::route('/{record}'),
            'edit' => Pages\EditOrganizationType::route('/{record}/edit'),
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
