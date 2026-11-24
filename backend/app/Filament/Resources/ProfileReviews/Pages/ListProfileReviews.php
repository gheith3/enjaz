<?php

namespace App\Filament\Resources\ProfileReviews\Pages;

use App\Filament\Resources\ProfileReviews\ProfileReviewResource;
use Filament\Resources\Pages\ListRecords;

class ListProfileReviews extends ListRecords
{
    protected static string $resource = ProfileReviewResource::class;

    public function getSubheading(): ?string
    {
        return 'كل روابط المراجعة. يُنشأ الرابط من «المنجزون حسب المجال» أو من صفحة المنجز (زر «رابط مراجعة الصفحة»).';
    }
}
