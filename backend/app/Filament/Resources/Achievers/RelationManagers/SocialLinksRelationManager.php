<?php

namespace App\Filament\Resources\Achievers\RelationManagers;

use App\Filament\Resources\SocialLinks\Schemas\SocialLinkForm;
use App\Filament\Resources\SocialLinks\Tables\SocialLinksTable;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

/** The achiever's social accounts (profile page). Same form as the site's accounts. */
class SocialLinksRelationManager extends RelationManager
{
    protected static string $relationship = 'socialLinks';

    protected static ?string $title = 'حسابات التواصل';

    protected static ?string $modelLabel = 'حساب';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return SocialLinkForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return SocialLinksTable::configure($table)
            ->headerActions([CreateAction::make()])
            ->pushRecordActions([DeleteAction::make()]);
    }
}
