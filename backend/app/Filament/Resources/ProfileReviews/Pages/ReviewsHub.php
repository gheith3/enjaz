<?php

namespace App\Filament\Resources\ProfileReviews\Pages;

use App\Filament\Resources\ProfileReviews\ProfileReviewResource;
use App\Support\ProfileReviews\ReviewBoard;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

/** «مراجعات المنجزين»: two tiles, المنجزون (with the stages of their reviews) and المؤسسات (next phase). */
class ReviewsHub extends Page
{
    protected static string $resource = ProfileReviewResource::class;

    protected string $view = 'filament.profile-reviews.hub';

    protected static ?string $title = 'مراجعات المنجزين';

    public function getSubheading(): ?string
    {
        return 'متابعة مراجعة الصفحات قبل التدشين.';
    }

    /** @return array{total: int, counts: array<string, int>} */
    public function summary(): array
    {
        $people = ReviewBoard::people();

        return ['total' => $people->count(), 'counts' => ReviewBoard::counts($people)];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('all')->label('كل الروابط')->icon('heroicon-o-list-bullet')->color('gray')
                ->url(ProfileReviewResource::getUrl('all')),
        ];
    }
}
