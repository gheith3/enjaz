<?php

namespace Tests\Feature\Site;

use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\Field;
use App\Models\ProfileReview;
use App\Models\Scope;
use App\Support\ProfileReviews\ProfileReviewApplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileReviewPageTest extends TestCase
{
    use RefreshDatabase;

    private const PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

    private Achiever $achiever;

    private Achievement $achievement;

    protected function setUp(): void
    {
        parent::setUp();
        $field = Field::factory()->create(['name' => ['ar' => 'الرياضة']]);
        $this->achiever = Achiever::factory()->create([
            'name' => ['ar' => 'علي بن محمد الحبسي'], 'field_id' => $field->id, 'story' => ['ar' => 'بدأت الحكاية في الحارة.'],
        ]);
        $this->achiever->journeyItems()->create(['year' => '2005', 'title' => ['ar' => 'أول مباراة'], 'icon' => 'ball', 'sort_order' => 1]);
        $this->achievement = Achievement::factory()->create(['title' => ['ar' => 'بطولة الخليج'], 'scope_id' => Scope::factory()->create()->id]);
        $this->achievement->achievers()->attach($this->achiever);
    }

    private function review(): ProfileReview
    {
        return ProfileReview::start($this->achiever);
    }

    /** Every point answered «صحيحة» except the given corrections. */
    private function answers(ProfileReview $review, array $edits = []): array
    {
        $data = ['d' => [], 'v' => []];
        foreach ($review->items as $item) {
            $data['d'][$item['key']] = array_key_exists($item['key'], $edits) ? 'edit' : 'ok';
        }
        foreach ($edits as $key => $value) {
            $data['v'][$key] = $value;
        }

        return $data;
    }

    public function test_page_lists_every_point_privately_and_marks_the_link_opened(): void
    {
        $review = $this->review();

        $this->get($review->url())->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertSee('أهلًا علي الحبسي')
            ->assertSee('الاسم الكامل')->assertSee('بدأت الحكاية في الحارة.')
            ->assertSee('2005 | أول مباراة')
            ->assertSee('بطولة الخليج')
            ->assertSee('إنجازات لم نذكرها')
            ->assertDontSee('site-header'); // never the site's layout

        $this->assertSame('opened', $review->fresh()->status);
    }

    public function test_unknown_token_is_not_found_and_an_expired_link_is_closed(): void
    {
        $this->get('/review/'.str_repeat('a', 48))->assertNotFound();
        $this->get('/review/short')->assertNotFound();

        $review = $this->review();
        $review->update(['expires_at' => now()->subDay()]);
        $this->get($review->url())->assertOk()->assertSee('انتهت صلاحية الرابط')->assertDontSee('الاسم الكامل');
        $this->post(route('profile-review.save', $review->token), $this->answers($review))->assertForbidden();
    }

    public function test_draft_keeps_answers_and_submit_needs_every_point(): void
    {
        $review = $this->review();
        $nameKey = "achiever_{$this->achiever->id}_name";

        $this->post(route('profile-review.save', $review->token), [
            'd' => [$nameKey => 'edit'], 'v' => [$nameKey => 'علي محمد الحبسي'], 'action' => 'draft',
        ])->assertRedirect();
        $item = collect($review->fresh()->items)->firstWhere('key', $nameKey);
        $this->assertSame(['edit', 'علي محمد الحبسي'], [$item['decision'], $item['new']]);
        $this->assertSame('sent', $review->fresh()->status);

        $this->post(route('profile-review.save', $review->token), [
            'd' => [$nameKey => 'edit'], 'v' => [$nameKey => 'علي محمد الحبسي'], 'action' => 'submit',
        ])->assertSessionHasErrors();
        $this->assertNotSame('submitted', $review->fresh()->status);

        $this->post(route('profile-review.save', $review->token), [
            ...$this->answers($review, [$nameKey => 'علي محمد الحبسي']),
            'add' => ['n1' => ['title' => 'ذهبية آسيا', 'y' => 2024, 'm' => 3, 'description' => "الفقرة الأولى\n\nالفقرة الثانية"]],
            'note' => 'شكرًا لكم',
            'action' => 'submit',
        ])->assertRedirect($review->url());

        $review->refresh();
        $this->assertSame('submitted', $review->status);
        $this->assertSame('2024-03', $review->additions[0]['when']);
        $this->assertSame('شكرًا لكم', $review->note);
        $this->get($review->url())->assertSee('شكرًا لك')->assertDontSee('الاسم الكامل');
    }

    public function test_images_upload_into_the_review_folder_only(): void
    {
        $review = $this->review();
        $file = UploadedFile::fake()->createWithContent('me.png', base64_decode(self::PNG));

        $path = $this->post(route('profile-review.upload', $review->token), ['image' => $file], ['Accept' => 'application/json'])
            ->assertOk()->json('path');
        $this->assertStringStartsWith("profile-reviews/{$review->id}/", $path);
        Storage::disk(ProfileReviewApplier::diskName())->assertExists($path);

        $this->post(route('profile-review.upload', $review->token), ['image' => UploadedFile::fake()->create('x.pdf', 10, 'application/pdf')], ['Accept' => 'application/json'])
            ->assertUnprocessable();

        $portrait = "achiever_{$this->achiever->id}_portrait";
        $this->post(route('profile-review.save', $review->token), ['d' => [$portrait => 'edit'], 'v' => [$portrait => 'profile-reviews/999/other.png']]);
        $this->assertNull(collect($review->fresh()->items)->firstWhere('key', $portrait)['new']);
    }

    public function test_approved_answers_go_live_and_the_rest_does_not(): void
    {
        $review = $this->review();
        $id = $this->achiever->id;
        $path = "profile-reviews/{$review->id}/me.png";
        Storage::disk(ProfileReviewApplier::diskName())->put($path, base64_decode(self::PNG));

        $edits = [
            "achiever_{$id}_name" => 'علي محمد الحبسي',
            "achiever_{$id}_story" => 'تعديل لم يُعتمد',
            "achiever_{$id}_journey_items" => "2003 | البداية في نادي النصر\n2010 - الاحتراف في إنجلترا",
            "achiever_{$id}_social_links" => "instagram.com/ali\nhttps://example.com",
            "achiever_{$id}_portrait" => $path,
            "achievement_{$this->achievement->id}_body" => "فقرة أولى\n\nفقرة <ثانية>",
            "achievement_{$this->achievement->id}_achieved_at" => '2011-05',
        ];
        $review->items = collect($review->items)->map(function (array $item) use ($edits) {
            $item['decision'] = array_key_exists($item['key'], $edits) ? 'edit' : 'ok';
            $item['new'] = $edits[$item['key']] ?? null;
            $item['approved'] = $item['decision'] === 'edit' ? ! str_ends_with($item['key'], '_story') : null;

            return $item;
        })->all();
        $review->additions = [
            ['uid' => 'n1', 'title' => 'ذهبية آسيا', 'when' => '2024', 'description' => 'وصف', 'approved' => true],
            ['uid' => 'n2', 'title' => 'لم تُعتمد', 'when' => '2023', 'approved' => false],
        ];
        $review->save();

        $result = ProfileReviewApplier::apply($review);

        $this->assertSame(['changes' => 6, 'additions' => 1], $result);
        $achiever = $this->achiever->fresh();
        $this->assertSame('علي محمد الحبسي', $achiever->getTranslation('name', 'ar'));
        $this->assertSame('بدأت الحكاية في الحارة.', $achiever->getTranslation('story', 'ar'));
        $this->assertSame([['2003', 'البداية في نادي النصر', 'ball'], ['2010', 'الاحتراف في إنجلترا', 'star']],
            $achiever->journeyItems->map(fn ($j) => [$j->year, $j->getTranslation('title', 'ar'), $j->icon])->all());
        $this->assertSame(['instagram', 'website'], $achiever->socialLinks->pluck('platform')->all());
        $this->assertSame('https://instagram.com/ali', $achiever->socialLinks->first()->url);
        $this->assertNotNull($achiever->getFirstMedia('portrait'));

        $achievement = $this->achievement->fresh();
        $this->assertSame('<p>فقرة أولى</p><p>فقرة &lt;ثانية&gt;</p>', $achievement->getTranslation('body', 'ar'));
        $this->assertSame(['2011-05-01', 'month'], [$achievement->achieved_at->format('Y-m-d'), $achievement->date_precision]);

        $added = $achiever->achievements()->where('title->ar', 'ذهبية آسيا')->first();
        $this->assertNotNull($added);
        $this->assertTrue($added->isLive());
        $this->assertSame('year', $added->date_precision);
        $this->assertFalse(Achievement::query()->where('title->ar', 'لم تُعتمد')->exists());
    }
}
