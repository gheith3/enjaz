<?php

namespace App\Filament\Resources\ProfileReviews\Pages;

use App\Filament\Resources\ProfileReviews\ProfileReviewResource;
use App\Models\Achiever;
use App\Models\Field;
use App\Models\ProfileReview;
use App\Support\ProfileReviews\ReviewBoard;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;

/** The achievers of one field, a tile each with the stage of their review; a tile opens the review (or creates the link). */
class ReviewFieldAchievers extends Page
{
    protected static string $resource = ProfileReviewResource::class;

    protected string $view = 'filament.profile-reviews.field';

    public string $field = '';

    public string $filter = 'all';

    public function mount(string $field): void
    {
        abort_unless($field === 'none' || Field::query()->whereKey((int) $field)->exists(), 404);
        $this->field = $field;
    }

    public function getTitle(): string|Htmlable
    {
        return $this->field === 'none' ? 'بلا مجال بعد' : (string) Field::query()->find((int) $this->field)?->getTranslation('name', 'ar', false);
    }

    public function getBreadcrumbs(): array
    {
        return [
            ProfileReviewResource::getUrl('index') => 'مراجعات المنجزين',
            ProfileReviewResource::getUrl('fields') => 'المنجزون',
            $this->getTitle(),
        ];
    }

    public function people(): Collection
    {
        return ReviewBoard::people($this->field);
    }

    /** Opens the achiever's latest review, or creates their link first. */
    public function open(int $achieverId): void
    {
        abort_unless(ProfileReviewResource::canViewAny(), 403);
        $achiever = Achiever::query()->findOrFail($achieverId);
        $review = ProfileReview::query()->where('achiever_id', $achiever->id)->latest('id')->first() ?? ProfileReview::start($achiever);

        $this->redirect(ProfileReviewResource::getUrl('review', ['record' => $review]));
    }
}
