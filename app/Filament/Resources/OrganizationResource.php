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

    protected static ?string $navigationIcon = 'heroicon-o-building-office';


    public static function getModelLabel(): string
    {
        return __('app.organizations.resource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.organizations.resource.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__("app.organizations_details"))
                    ->schema([
                        Forms\Components\Select::make('organization_type_id')
                            ->label(__('app.type'))
                            ->relationship('organizationType', 'title_en'),
                        Forms\Components\TextInput::make('website_url')
                            ->label(__('app.organizations.fields.website'))
                            ->url()
                            ->required(),

                        Forms\Components\TextInput::make('name_ar')
                            ->label(__('app.organizations.fields.name_ar'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('name_en')
                            ->label(__('app.organizations.fields.name_en'))
                            ->nullable()
                            ->maxLength(255),

                        Forms\Components\RichEditor::make('description_ar')
                            ->label(__('app.description_ar'))
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('description_en')
                            ->label(__('app.description_en'))
                            ->nullable()
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('logo_path')
                            ->label('Logo')
                            ->label(__('app.organizations.fields.logo'))
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull()
                            ->maxSize(1024 * 5)
                            ->required()
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('app.organizations.fields.manager'))
                    ->schema([
                        Forms\Components\TextInput::make('manager_name_ar')
                            ->label(__('app.organizations.fields.name_ar'))
                            ->nullable()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('manager_name_en')
                            ->label(__('app.organizations.fields.name_en'))
                            ->nullable()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('manager_message_ar')
                            ->label(__('app.organizations.fields.manager_message_ar'))
                            ->nullable()
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('manager_message_en')
                            ->label(__('app.organizations.fields.manager_message_en'))
                            ->nullable()
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('manager_image_path')
                            ->label(__('app.organizations.fields.manager_image'))
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
                Tables\Columns\TextColumn::make('name_ar')
                    ->label(__('app.organizations.table.name_ar'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('name_en')
                    ->label(__('app.organizations.table.name_en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('app.organizations.table.created_at'))
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
