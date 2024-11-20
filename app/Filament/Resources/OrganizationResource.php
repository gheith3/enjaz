<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizationResource\Pages;
use App\Filament\Resources\OrganizationResource\RelationManagers;
use App\Models\Organization;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Organization Information')
                    ->schema([
                        Forms\Components\Select::make('organization_type_id')
                            ->relationship('organizationType', 'title_en')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('website_url')
                            ->url()
                            ->required(),
                        Forms\Components\FileUpload::make('logo_path')
                            ->label('Logo')
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull()
                            ->maxSize(1024 * 5)
                            ->required(),

                    ])
                    ->columns(2),

                Forms\Components\Section::make('Organization Information')
                    ->schema([
                        Forms\Components\TextInput::make('name_ar')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('description_ar')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Organization Information')
                    ->schema([
                        Forms\Components\TextInput::make('name_en')
                            ->nullable()
                            ->maxLength(255),

                        Forms\Components\RichEditor::make('description_en')
                            ->nullable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Organization Manager')
                    ->schema([
                        Forms\Components\TextInput::make('manager_name_ar')
                            ->nullable()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('manager_name_en')
                            ->nullable()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('manager_message_ar')
                            ->nullable()
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('manager_message_en')
                            ->nullable()
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('manager_image_path')
                            ->label('manager image')
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull()
                            ->maxSize(1024 * 5)
                            ->nullable(),
                    ])->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('organizationType.title_ar')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'view' => Pages\ViewOrganization::route('/{record}'),
            'edit' => Pages\EditOrganization::route('/{record}/edit'),
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
