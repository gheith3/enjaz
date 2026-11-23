<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Achievers\Pages\EditAchiever;
use App\Filament\Resources\ProfileReviews\Pages\ReviewProfileReview;
use App\Filament\Resources\ProfileReviews\ProfileReviewResource;
use App\Models\Achiever;
use App\Models\ProfileReview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileReviewsDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_creates_a_link_from_the_achiever_and_sees_it(): void
    {
        $this->actingAs($this->userWithRole('editor'));
        $achiever = Achiever::factory()->create(['name' => ['ar' => 'جوخة الحارثي']]);

        Livewire::test(EditAchiever::class, ['record' => $achiever->getRouteKey()])->callAction('reviewLink');

        $review = ProfileReview::query()->sole();
        $this->assertSame($achiever->id, $review->achiever_id);
        $this->assertNotEmpty($review->items);

        // A second click opens the same open review instead of creating another.
        Livewire::test(EditAchiever::class, ['record' => $achiever->getRouteKey()])->callAction('reviewLink');
        $this->assertSame(1, ProfileReview::query()->count());

        $this->get(ProfileReviewResource::getUrl('index'))->assertOk()->assertSee('جوخة الحارثي');
        $this->get(ProfileReviewResource::getUrl('review', ['record' => $review]))->assertOk()
            ->assertSee($review->url())->assertSee('واتساب');
    }

    public function test_approving_publishes_only_the_selected_corrections(): void
    {
        $this->actingAs($this->userWithRole('editor'));
        $achiever = Achiever::factory()->create(['name' => ['ar' => 'الاسم القديم'], 'headline' => ['ar' => 'الوصف القديم']]);
        $review = ProfileReview::start($achiever);
        $name = "achiever_{$achiever->id}_name";
        $headline = "achiever_{$achiever->id}_headline";
        $review->update([
            'status' => 'submitted',
            'items' => collect($review->items)->map(fn ($item) => [
                ...$item,
                'decision' => in_array($item['key'], [$name, $headline], true) ? 'edit' : 'ok',
                'new' => match ($item['key']) { $name => 'الاسم الجديد', $headline => 'وصف جديد', default => null },
            ])->all(),
        ]);

        Livewire::test(ReviewProfileReview::class, ['record' => $review->getRouteKey()])
            ->assertSee('الاسم الجديد')
            ->set("approvals.$headline", false)
            ->callAction('approve')
            ->assertHasNoActionErrors();

        $achiever->refresh();
        $this->assertSame('الاسم الجديد', $achiever->getTranslation('name', 'ar'));
        $this->assertSame('الوصف القديم', $achiever->getTranslation('headline', 'ar'));
        $this->assertSame('approved', $review->fresh()->status);
    }

    public function test_returning_a_review_reopens_the_link_with_a_note(): void
    {
        $this->actingAs($this->userWithRole('editor'));
        $review = ProfileReview::factory()->create(['status' => 'submitted']);

        Livewire::test(ReviewProfileReview::class, ['record' => $review->getRouteKey()])
            ->callAction('return', ['admin_note' => 'أرسل صورة أوضح'])
            ->assertHasNoActionErrors();

        $review->refresh();
        $this->assertSame('returned', $review->status);
        $this->assertTrue($review->isEditable());
        $this->get($review->url())->assertSee('أرسل صورة أوضح');
    }
}
