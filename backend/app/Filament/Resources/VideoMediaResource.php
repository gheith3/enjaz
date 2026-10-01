<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoMediaResource\Pages;
use App\Filament\Resources\VideoMediaResource\RelationManagers;
use App\Models\VideoMedia;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VideoMediaResource extends Resource
{
    protected static ?string $model = VideoMedia::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('app.video_media.resource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.video_media.resource.plural_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.group.media');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                ->schema([
                    TextInput::make('title')
                        ->label(__('app.video_media.fields.title'))
                        ->required(),
                    TextInput::make('url')
                        ->label(__('app.video_media.fields.video_url'))
                        ->url()
                        ->required(),
                    Textarea::make('description')
                        ->label(__('app.video_media.fields.description'))
                        ->rows(4)
                        ->columnSpanFull()
                        ->required(),
                    FileUpload::make('cover_image')
                        ->label(__('app.video_media.fields.thumbnail'))
                        ->image()
                        ->imageEditor()
                        ->columnSpanFull()
                        ->maxSize(1024 * 5)
                        ->required(),
                    Forms\Components\Toggle::make('is_active')
                        ->label(__('app.video_media.fields.is_active'))
                        ->required(),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('app.video_media.table.title'))
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('app.video_media.table.is_active'))
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListVideoMedia::route('/'),
            'create' => Pages\CreateVideoMedia::route('/create'),
//            'view' => Pages\ViewVideoMedia::route('/{record}'),
            'edit' => Pages\EditVideoMedia::route('/{record}/edit'),
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
