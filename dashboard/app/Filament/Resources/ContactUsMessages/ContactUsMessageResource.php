<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactUsMessages;

use App\Enums\ContactMessageStatus;
use App\Filament\Resources\ContactUsMessages\Pages\ListContactUsMessages;
use App\Filament\Resources\ContactUsMessages\Pages\ViewContactUsMessage;
use App\Filament\Resources\ContactUsMessages\Schemas\ContactUsMessageInfolist;
use App\Filament\Resources\ContactUsMessages\Tables\ContactUsMessagesTable;
use App\Models\ContactUsMessage;
use BackedEnum;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

/**
 * Contact Us Message Resource for viewing incoming contact messages.
 */
class ContactUsMessageResource extends Resource
{
    protected static ?string $model = ContactUsMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    public static function getNavigationGroup(): ?string
    {
        return __('common.nav_groups.operations');
    }

    protected static ?int $navigationSort = 100;

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Get the navigation badge count (new messages only).
     */
    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', ContactMessageStatus::New)->count();
    }

    /**
     * Get the navigation badge color.
     */
    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    /**
     * Get the navigation badge tooltip.
     */
    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('contact.navigation.badge_tooltip');
    }

    /**
     * Configure the infolist schema.
     */
    public static function infolist(Schema $schema): Schema
    {
        return ContactUsMessageInfolist::configure($schema);
    }

    /**
     * Configure the table.
     */
    public static function table(Table $table): Table
    {
        return ContactUsMessagesTable::configure($table);
    }

    /**
     * Get the resource pages.
     *
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListContactUsMessages::route('/'),
            'view' => ViewContactUsMessage::route('/{record}'),
        ];
    }

    /**
     * Get the model label.
     */
    public static function getModelLabel(): string
    {
        return __('contact.model_label');
    }

    /**
     * Get the plural model label.
     */
    public static function getPluralModelLabel(): string
    {
        return __('contact.model_label_plural');
    }

    /**
     * Get the navigation label.
     */
    public static function getNavigationLabel(): string
    {
        return __('contact.model_label_plural');
    }
}
