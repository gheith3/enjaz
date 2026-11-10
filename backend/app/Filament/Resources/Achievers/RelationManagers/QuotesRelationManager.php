<?php

namespace App\Filament\Resources\Achievers\RelationManagers;

use App\Filament\Support\MediaSlot;
use App\Filament\Support\Translatable;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** "قالوا عن ...": what others said about the achiever. */
class QuotesRelationManager extends RelationManager
{
    protected static string $relationship = 'quotes';

    protected static ?string $title = 'قالوا عنه';

    protected static ?string $modelLabel = 'اقتباس';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Translatable::textarea('text', 'النص', required: true),
            Translatable::text('name', 'القائل'),
            Translatable::text('role', 'صفته', required: false),
            MediaSlot::make('avatar', 'صورة القائل', 'اتركها فارغة لتظهر صورة رمزية.', focal: false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->paginated(false)
            ->columns([
                SpatieMediaLibraryImageColumn::make('avatar')->collection('avatar')->label('')->circular()->height(32),
                TextColumn::make('name')->label('القائل'),
                TextColumn::make('text')->label('النص')->limit(80),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
