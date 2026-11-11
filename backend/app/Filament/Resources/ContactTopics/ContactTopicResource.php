<?php

namespace App\Filament\Resources\ContactTopics;

use App\Filament\Resources\ContactTopics\Pages\ManageContactTopics;
use App\Filament\Support\Translatable;
use App\Models\ContactTopic;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

/** Topics of the contact form. */
class ContactTopicResource extends Resource
{
    protected static ?string $model = ContactTopic::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static string|UnitEnum|null $navigationGroup = 'التصنيفات';

    protected static ?int $navigationSort = 9;

    protected static ?string $modelLabel = 'موضوع';

    protected static ?string $pluralModelLabel = 'مواضيع التواصل';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Translatable::text('name', 'الموضوع')]);
    }

    public static function table(Table $table): Table
    {
        return $table->reorderable('sort_order')->defaultSort('sort_order')->paginated(false)
            ->columns([TextColumn::make('name')->label('الموضوع'), ToggleColumn::make('is_active')->label('ظاهر')])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageContactTopics::route('/')];
    }
}
