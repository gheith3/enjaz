<?php

namespace App\Filament\Resources\Achievers\RelationManagers;

use App\Filament\Support\RelationManagers\GalleryRelationManager as BaseGallery;

/** "المكتبة المرئية" on the profile. */
class GalleryRelationManager extends BaseGallery
{
    protected static string $relationship = 'galleryMedia';

    protected static ?string $title = 'المكتبة المرئية';
}
