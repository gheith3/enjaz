<?php

namespace App\Filament\Resources\Achievements\RelationManagers;

use App\Filament\Support\Translatable;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** "المصادر" at the end of the achievement story. */
class SourcesRelationManager extends RelationManager
{
    protected static string $relationship = 'sources';

    protected static ?string $title = 'المصادر';

    protected static ?string $modelLabel = 'مصدر';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Translatable::text('title', 'الجهة (مثل: الجزيرة)'),
            Translatable::text('subtitle', 'عنوان المادة', required: false, maxLength: 500),
            TextInput::make('url')->label('الرابط')->url()->maxLength(1000)->columnSpanFull()->extraInputAttributes(['dir' => 'ltr']),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->paginated(false)
            ->columns([
                TextColumn::make('title')->label('الجهة'),
                TextColumn::make('subtitle')->label('العنوان')->limit(60),
                TextColumn::make('url')->label('الرابط')->limit(40)->url(fn ($record) => $record->url, shouldOpenInNewTab: true),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
