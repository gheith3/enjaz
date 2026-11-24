<?php

namespace App\Filament\Resources\ProfileReviews\Pages;

use App\Filament\Resources\ProfileReviews\ProfileReviewResource;
use App\Support\ProfileReviews\ReviewBoard;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;

/** «المنجزون حسب المجال»: a tile per field with its achievers and the stages of their reviews. */
class ReviewFields extends Page
{
    protected static string $resource = ProfileReviewResource::class;

    protected string $view = 'filament.profile-reviews.fields';

    protected static ?string $title = 'المنجزون حسب المجال';

    public function getSubheading(): ?string
    {
        return 'اختر مجالًا لرؤية منجزيه وحالة مراجعة كل واحد.';
    }

    public function getBreadcrumbs(): array
    {
        return [ProfileReviewResource::getUrl('index') => 'مراجعات المنجزين', 'المنجزون'];
    }

    public function fields(): Collection
    {
        return ReviewBoard::fields();
    }
}
