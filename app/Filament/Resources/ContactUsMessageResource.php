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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
//                Tables\Actions\EditAction::make(),
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
        ];
    }
}
