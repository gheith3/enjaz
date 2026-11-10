<?php

namespace App\Filament\Resources\AwardWinners\RelationManagers;

use App\Filament\Support\RelationManagers\GalleryRelationManager as BaseGallery;

class DesignsRelationManager extends BaseGallery
{
    protected static string $relationship = 'designMedia';

    protected static string $collection = 'designs';

    protected static ?string $title = 'صور التصميم الفائز';
}
