<?php

namespace App\Filament\Resources\News\RelationManagers;

use App\Filament\Support\RelationManagers\GalleryRelationManager;

/** Photos under the news story. */
class GalleryMediaRelationManager extends GalleryRelationManager
{
    protected static string $relationship = 'galleryMedia';

    protected static ?string $title = 'صور الخبر';
}
