<?php

declare(strict_types=1);

namespace App\Filament\Resources\TeamMembers;

use App\Filament\Resources\TeamMembers\Pages\CreateTeamMember;
use App\Filament\Resources\TeamMembers\Pages\EditTeamMember;
use App\Filament\Resources\TeamMembers\Pages\ListTeamMembers;
use App\Filament\Resources\TeamMembers\Pages\ViewTeamMember;
use App\Filament\Resources\TeamMembers\Schemas\TeamMemberForm;
use App\Filament\Resources\TeamMembers\Schemas\TeamMemberInfolist;
use App\Filament\Resources\TeamMembers\Tables\TeamMembersTable;
use App\Models\TeamMember;
use BackedEnum;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

/**
 * Team Member Resource for managing team members (فريق العمل).
 */
class TeamMemberResource extends Resource
{
    protected static ?string $model = TeamMember::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    public static function getNavigationGroup(): ?string
    {
        return __('common.nav_groups.people');
    }

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name_ar';

    /**
     * Get the navigation badge count.
     */
    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    /**
     * Get the navigation badge color.
     */
    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }

    /**
     * Configure the form schema.
     */
    public static function form(Schema $schema): Schema
    {
        return TeamMemberForm::configure($schema);
    }

    /**
     * Configure the infolist schema.
     */
    public static function infolist(Schema $schema): Schema
    {
        return TeamMemberInfolist::configure($schema);
    }

    /**
     * Configure the table.
     */
    public static function table(Table $table): Table
    {
        return TeamMembersTable::configure($table);
    }

    /**
     * Get the resource relations.
     *
     * @return array<class-string>
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * Get the resource pages.
     *
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListTeamMembers::route('/'),
            'create' => CreateTeamMember::route('/create'),
            'view' => ViewTeamMember::route('/{record}'),
            'edit' => EditTeamMember::route('/{record}/edit'),
        ];
    }

    /**
     * Get the globally searchable attributes.
     *
     * @return array<string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['name_ar', 'name_en', 'position_ar', 'position_en'];
    }

    /**
     * Get the model label.
     */
    public static function getModelLabel(): string
    {
        return __('team_members.model_label');
    }

    /**
     * Get the plural model label.
     */
    public static function getPluralModelLabel(): string
    {
        return __('team_members.model_label_plural');
    }

    /**
     * Get the navigation label.
     */
    public static function getNavigationLabel(): string
    {
        return __('team_members.model_label_plural');
    }
}
