<?php

namespace App\Filament\Resources\AwardFeatures\RelationManagers;

use App\Filament\Support\RelationManagers\GalleryRelationManager as BaseGallery;

class ImagesRelationManager extends BaseGallery
{
    protected static string $relationship = 'imageMedia';

    protected static string $collection = 'images';

    protected static ?string $title = 'الصور';
}
