<?php

namespace App\Filament\Resources\ProfileReviews\Pages;

use App\Filament\Resources\Achievers\AchieverResource;
use App\Filament\Resources\ProfileReviews\ProfileReviewResource;
use App\Models\ProfileReview;
use App\Support\ProfileReviews\ProfileReviewApplier;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;

/**
 * One achiever's review: the link to send (copy, WhatsApp, e-mail), every correction beside the current text
 * with a box to approve it, the achievements they added, then «اعتماد المحدد ونشره» or «إعادة للمنجز».
 *
 * @property ProfileReview $record
 */
class ReviewProfileReview extends Page
{
    use InteractsWithRecord;

    protected static string $resource = ProfileReviewResource::class;

    protected string $view = 'filament.profile-reviews.review';

    /** @var array<string, bool> item key => approved */
    public array $approvals = [];

    /** @var array<int, bool> addition index => approved */
    public array $additionApprovals = [];

    /** @var array<string, bool> journey | socials | videos | contact => approved */
    public array $partApprovals = [];

    /** @var array<int, bool> gallery photo index => approved */
    public array $photoApprovals = [];

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        abort_unless(static::getResource()::canView($this->record), 403);
        $this->fillApprovals();
    }

    public function getTitle(): string|Htmlable
    {
        return 'مراجعة: '.($this->record->achiever?->getTranslation('name', 'ar', false) ?? '');
    }

    public function getSubheading(): ?string
    {
        return $this->record->statusLabel();
    }

    /** @return Collection<string, Collection<int, array<string, mixed>>> edited points grouped by section */
    public function editedSections(): Collection
    {
        return collect($this->record->items)->where('decision', 'edit')->groupBy('title');
    }

    public function newUrl(?string $path): ?string
    {
        return filled($path) ? ProfileReviewApplier::disk()->url($path) : null;
    }

    public function selectAll(bool $value): void
    {
        $this->approvals = array_map(fn () => $value, $this->approvals);
        $this->additionApprovals = array_map(fn () => $value, $this->additionApprovals);
        $this->partApprovals = array_map(fn () => $value, $this->partApprovals);
        $this->photoApprovals = array_map(fn () => $value, $this->photoApprovals);
    }

    protected function getHeaderActions(): array
    {
        $canUpdate = fn () => ProfileReviewResource::canEdit($this->record);

        return [
            Action::make('approve')->label('اعتماد المحدد ونشره')->icon('heroicon-o-check-badge')->color('success')
                ->visible(fn () => $canUpdate() && $this->record->status === 'submitted')
                ->requiresConfirmation()
                ->modalHeading('اعتماد المراجعة')
                ->modalDescription(fn () => 'سيُنشر في الموقع فورًا ما حددته: تعديلات ('.(count(array_filter($this->approvals)) + count(array_filter($this->partApprovals)))
                    .')، صور ('.count(array_filter($this->photoApprovals)).')، إنجازات مضافة ('.count(array_filter($this->additionApprovals)).'). ما لم يُحدَّد لا يُنشر.')
                ->modalSubmitActionLabel('اعتماد ونشر')
                ->action(fn () => $this->approve()),
            Action::make('return')->label('إعادة للمنجز')->icon('heroicon-o-arrow-uturn-right')->color('warning')
                ->visible(fn () => $canUpdate() && in_array($this->record->status, ['submitted', 'approved'], true))
                ->schema([
                    Textarea::make('admin_note')->label('ملاحظة تظهر للمنجز أعلى الصفحة')->rows(4)->required()
                        ->default(fn () => $this->record->admin_note),
                ])
                ->modalDescription('يعود الرابط قابلًا للتعديل مع إجاباته السابقة، ويُمدّد ١٤ يومًا على الأقل.')
                ->modalSubmitActionLabel('إعادة')
                ->action(function (array $data) {
                    $this->record->update([
                        'status' => 'returned',
                        'admin_note' => $data['admin_note'],
                        'expires_at' => max(now()->addDays(14), $this->record->expires_at ?? now()),
                    ]);
                    Notification::make()->title('أُعيدت المراجعة للمنجز، أرسل له الرابط مرة أخرى.')->success()->send();
                }),
            Action::make('extend')->label('تمديد الرابط')->icon('heroicon-o-clock')->color('gray')
                ->visible(fn () => $canUpdate() && in_array($this->record->status, ['sent', 'opened', 'returned'], true))
                ->action(function () {
                    $this->record->update(['expires_at' => now()->addDays(ProfileReview::VALID_DAYS)]);
                    Notification::make()->title('مُدّد الرابط '.ProfileReview::VALID_DAYS.' يومًا.')->success()->send();
                }),
            Action::make('achiever')->label('صفحة المنجز')->icon('heroicon-o-user')->color('gray')
                ->url(fn () => $this->record->achiever ? AchieverResource::getUrl('edit', ['record' => $this->record->achiever]) : null),
            DeleteAction::make()->record($this->record)->successRedirectUrl(ProfileReviewResource::getUrl('all')),
        ];
    }

    private function approve(): void
    {
        $review = $this->record;
        $review->items = collect($review->items)->map(function (array $item) {
            $item['approved'] = $item['decision'] === 'edit' ? (bool) ($this->approvals[$item['key']] ?? false) : null;

            return $item;
        })->all();
        $review->additions = collect($review->additions ?? [])->map(function (array $addition, int $index) {
            $addition['approved'] = (bool) ($this->additionApprovals[$index] ?? false);

            return $addition;
        })->all();
        foreach (['journey', 'socials', 'videos', 'contact'] as $part) {
            $review->{$part} = [...($review->{$part} ?? []), 'approved' => (bool) ($this->partApprovals[$part] ?? false)];
        }
        $review->gallery = collect($review->gallery ?? [])->map(function (array $photo, int $index) {
            $photo['approved'] = (bool) ($this->photoApprovals[$index] ?? false);

            return $photo;
        })->all();
        $review->save();

        $result = ProfileReviewApplier::apply($review);

        $review->update(['status' => 'approved', 'reviewed_at' => now(), 'reviewed_by' => auth()->id()]);
        $this->record->refresh();
        $this->fillApprovals();

        Notification::make()->success()->title('اعتُمدت المراجعة ونُشرت')
            ->body("تعديلات منشورة: {$result['changes']} · صور: {$result['photos']} · إنجازات جديدة: {$result['additions']}")->send();
    }

    private function fillApprovals(): void
    {
        $this->approvals = collect($this->record->items)->where('decision', 'edit')
            ->mapWithKeys(fn (array $item) => [$item['key'] => $item['approved'] ?? true])->all();
        $this->additionApprovals = collect($this->record->additions ?? [])
            ->map(fn (array $addition) => $addition['approved'] ?? true)->all();
        $this->partApprovals = collect(['journey', 'socials', 'videos', 'contact'])
            ->filter(fn ($part) => $this->record->changed($part))
            ->mapWithKeys(fn ($part) => [$part => $this->record->{$part}['approved'] ?? true])->all();
        $this->photoApprovals = collect($this->record->gallery ?? [])
            ->map(fn (array $photo) => $photo['approved'] ?? true)->all();
    }
}
