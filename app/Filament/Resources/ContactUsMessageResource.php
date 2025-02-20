<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactUsMessageResource\Pages;
use App\Filament\Resources\ContactUsMessageResource\RelationManagers;
use App\Models\ContactUsMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContactUsMessageResource extends Resource
{
    protected static ?string $model = ContactUsMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

  
    public static function getModelLabel(): string
    {
        return __('app.contact_us_messages.resource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.contact_us_messages.resource.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('app.contact_us_messages.fields.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label(__('app.contact_us_messages.fields.email'))
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('message')
                    ->label(__('app.contact_us_messages.fields.message'))
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->label(__('app.contact_us_messages.fields.status'))
                    ->options([
                        'new' => __('app.contact_us_messages.statuses.new'),
                        'read' => __('app.contact_us_messages.statuses.read'),
                        'in_progress' => __('app.contact_us_messages.statuses.in_progress'),
                        'completed' => __('app.contact_us_messages.statuses.completed'),
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('app.contact_us_messages.table.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('app.contact_us_messages.table.email'))
                    ->searchable(),
                Tables\Columns\SelectColumn::make('status')
                    ->label(__('app.contact_us_messages.table.status'))
                    ->options([
                        'new' => __('app.contact_us_messages.statuses.new'),
                        'read' => __('app.contact_us_messages.statuses.read'),
                        'in_progress' => __('app.contact_us_messages.statuses.in_progress'),
                        'completed' => __('app.contact_us_messages.statuses.completed'),
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('app.contact_us_messages.table.created_at'))
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('')
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('email'),
                    TextEntry::make('email'),
                ])->columns(3),
                Section::make('')
                    ->schema([
                        TextEntry::make('message'),
                    ])
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
            'index' => Pages\ListContactUsMessages::route('/'),
            // 'create' => Pages\CreateContactUsMessage::route('/create'),
            // 'view' => Pages\ViewContactUsMessage::route('/{record}'),
            // 'edit' => Pages\EditContactUsMessage::route('/{record}/edit'),
        ];
    }
}
