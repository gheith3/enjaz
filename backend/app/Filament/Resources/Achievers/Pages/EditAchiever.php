<?php

namespace App\Filament\Resources\Achievers\Pages;

use App\Filament\Resources\Achievers\AchieverResource;
use App\Filament\Resources\ProfileReviews\ProfileReviewResource;
use App\Models\ProfileReview;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditAchiever extends EditRecord
{
    protected static string $resource = AchieverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->reviewLinkAction(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    /**
     * «رابط مراجعة الصفحة»: opens the achiever's open review, or creates one with a snapshot of the profile as it is now.
     */
    private function reviewLinkAction(): Action
    {
        $open = fn () => ProfileReview::query()->where('achiever_id', $this->getRecord()->getKey())
            ->whereIn('status', ['sent', 'opened', 'returned', 'submitted'])->latest()->first();

        return Action::make('reviewLink')->label('رابط مراجعة الصفحة')->icon('heroicon-o-link')->color('warning')
            ->visible(fn () => ProfileReviewResource::canViewAny())
            ->requiresConfirmation(fn () => ! $open())
            ->modalHeading('رابط مراجعة للمنجز')
            ->modalDescription('يُنشأ رابط خاص يراجع فيه المنجز كل نقطة في صفحته ويضيف إنجازاته. احفظ تعديلاتك هنا أولًا: الرابط يعرض الصفحة كما هي الآن.')
            ->modalSubmitActionLabel('إنشاء الرابط')
            ->action(function () use ($open) {
                $review = $open() ?? ProfileReview::start($this->getRecord());

                $this->redirect(ProfileReviewResource::getUrl('review', ['record' => $review]));
            });
    }
}
