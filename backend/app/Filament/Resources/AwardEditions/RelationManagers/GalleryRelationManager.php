<?php

namespace App\Filament\Resources\AwardEditions\RelationManagers;

use App\Filament\Support\RelationManagers\GalleryRelationManager as BaseGallery;

class GalleryRelationManager extends BaseGallery
{
    protected static string $relationship = 'galleryMedia';

    protected static string $collection = 'gallery';

    protected static ?string $title = 'صور الدورة (المشروع والتتويج)';
}
