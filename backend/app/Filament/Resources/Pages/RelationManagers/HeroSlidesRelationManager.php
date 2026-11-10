<?php

namespace App\Filament\Resources\Pages\RelationManagers;

use App\Filament\Support\RelationManagers\GalleryRelationManager;
use App\Models\Page;
use Illuminate\Database\Eloquent\Model;

/** Images of the page's hero slider (only on pages whose design has one, e.g. المؤسسات). */
class HeroSlidesRelationManager extends GalleryRelationManager
{
    protected static string $relationship = 'heroMedia';

    protected static string $collection = 'hero';

    protected static ?string $title = 'صور رأس الصفحة';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord instanceof Page && $ownerRecord->hasFeature('hero_slides');
    }
}
