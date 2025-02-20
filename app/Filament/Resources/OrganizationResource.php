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
                Forms\Components\Section::make(__('app.organizations.sections.arabic_details'))
                    ->schema([
                        Forms\Components\TextInput::make('name_ar')
                            ->label(__('app.organizations.fields.name_ar'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('description_ar')
                            ->label(__('app.organizations.fields.description_ar'))
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('app.organizations.sections.english_details'))
                    ->schema([
                        Forms\Components\TextInput::make('name_en')
                            ->label(__('app.organizations.fields.name_en'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('description_en')
                            ->label(__('app.organizations.fields.description_en'))
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('app.organizations.sections.media'))
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->label(__('app.organizations.fields.logo'))
                            ->image()
                            ->imageEditor()
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('app.organizations.sections.basic_details'))
                    ->schema([
                        Forms\Components\TextInput::make('website')
                            ->label(__('app.organizations.fields.website'))
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label(__('app.organizations.fields.email'))
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('app.organizations.fields.phone'))
                            ->tel()
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label(__('app.organizations.fields.status'))
                            ->options([
                                'active' => __('app.organizations.statuses.active'),
                                'inactive' => __('app.organizations.statuses.inactive'),
                            ])
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('app.organizations.sections.social_media'))
                    ->schema([
                        Forms\Components\TextInput::make('facebook')
                            ->label(__('app.organizations.fields.facebook'))
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('twitter')
                            ->label(__('app.organizations.fields.twitter'))
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('instagram')
                            ->label(__('app.organizations.fields.instagram'))
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('linkedin')
                            ->label(__('app.organizations.fields.linkedin'))
                            ->url()
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_ar')
                    ->label(__('app.organizations.table.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('app.organizations.table.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('app.organizations.table.phone'))
                    ->searchable(),
                Tables\Columns\SelectColumn::make('status')
                    ->label(__('app.organizations.table.status'))
                    ->options([
                        'active' => __('app.organizations.statuses.active'),
                        'inactive' => __('app.organizations.statuses.inactive'),
                    ]),
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
