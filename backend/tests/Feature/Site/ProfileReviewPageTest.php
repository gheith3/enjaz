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
        $this->achiever->socialLinks()->create(['platform' => 'instagram', 'url' => 'https://instagram.com/ali', 'is_active' => true, 'sort_order' => 1]);
        $this->achievement = Achievement::factory()->create(['title' => ['ar' => 'بطولة الخليج'], 'scope_id' => Scope::factory()->create()->id]);
        $this->achievement->achievers()->attach($this->achiever);
    }

    private function review(): ProfileReview
    {
        return ProfileReview::start($this->achiever);
    }

    private function words(int $n): string
    {
        return trim(str_repeat('كلمة ', $n));
    }

    public function test_page_lists_the_profile_privately_and_marks_the_link_opened(): void
    {
        $review = $this->review();

        $this->get($review->url())->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertSee('علي بن محمد الحبسي')
            ->assertSee('الاسم الكامل')->assertSee('بدأت الحكاية في الحارة.')
            ->assertSee('أول مباراة')
            ->assertSee('https://instagram.com/ali')
            ->assertSee('بطولة الخليج')
            ->assertSee('أرسل صورة')->assertSee('أرسل لنا هذه الصورة من جهازك')
            ->assertSee('محطات مسيرتك')->assertSee('المكتبة المرئية')->assertSee('المقابلات واللقاءات المرئية')->assertSee('التواصل معك');

        $this->assertSame('opened', $review->fresh()->status);
    }

    public function test_unknown_token_is_not_found_and_an_expired_link_is_closed(): void
    {
        $this->get('/review/'.str_repeat('a', 48))->assertNotFound();
        $this->get('/review/short')->assertNotFound();

        $review = $this->review();
        $review->update(['expires_at' => now()->subDay()]);
        $this->get($review->url())->assertOk()->assertSee('انتهت صلاحية الرابط')->assertDontSee('الاسم الكامل');
        $this->post(route('profile-review.save', $review->token), ['action' => 'submit'])->assertForbidden();
    }

    public function test_untouched_points_count_as_correct_and_edits_are_kept(): void
    {
        $review = $this->review();
        $nameKey = "achiever_{$this->achiever->id}_name";

        $portraitKey = "achiever_{$this->achiever->id}_portrait";
        $coverKey = "achievement_{$this->achievement->id}_cover";
        $folder = "profile-reviews/{$review->id}";

        // Phone, e-mail and every photo are required.
        $this->post(route('profile-review.save', $review->token), ['action' => 'submit'])
            ->assertSessionHasErrors(['phone', 'email', "v.$portraitKey", "v.$coverKey"]);

        $this->post(route('profile-review.save', $review->token), [
            'd' => [$nameKey => 'edit', $portraitKey => 'edit', $coverKey => 'edit'],
            'v' => [$nameKey => 'علي محمد الحبسي', $portraitKey => "$folder/me.png", $coverKey => "$folder/cup.png"],
            'j' => ['s0' => ['y' => 2005, 'text' => 'أول مباراة مع النادي'], 's1' => ['y' => 2010, 'text' => 'الانضمام إلى المنتخب الوطني']],
            'social' => ['instagram' => 'https://instagram.com/ali', 'x' => '@ali'],
            'vid' => ['v0' => ['title' => 'لقاء تلفزيوني', 'url' => 'https://youtu.be/abcdefghijk']],
            'phone' => '+968 9000 0000', 'email' => 'ali@example.com',
            'add' => ['n1' => ['title' => 'ذهبية آسيا', 'y' => 2024, 'm' => 3, 'description' => $this->words(130), 'cover' => "$folder/asia.png"]],
            'action' => 'submit',
        ])->assertSessionHasNoErrors()->assertRedirect($review->url());

        $review->refresh();
        $this->assertSame('submitted', $review->status);
        $items = collect($review->items);
        $this->assertSame(['edit', 'علي محمد الحبسي'], [$items->firstWhere('key', $nameKey)['decision'], $items->firstWhere('key', $nameKey)['new']]);
        $this->assertSame('ok', $items->firstWhere('key', "achiever_{$this->achiever->id}_story")['decision']);
        $this->assertCount(2, $review->journey['new']);
        $this->assertTrue($review->changed('journey'));
        $this->assertSame('@ali', $review->socials['new']['x']);
        $this->assertSame('2024-03', $review->additions[0]['when']);
        $this->get($review->url())->assertSee('شكرًا لك')->assertDontSee('الاسم الكامل');
    }

    public function test_submit_checks_word_limits(): void
    {
        $review = $this->review();
        $bodyKey = "achievement_{$this->achievement->id}_body";

        $this->post(route('profile-review.save', $review->token), [
            'd' => [$bodyKey => 'edit'], 'v' => [$bodyKey => $this->words(20)],
            'j' => ['s0' => ['y' => 2005, 'text' => $this->words(9)]],
            'action' => 'submit',
        ])->assertSessionHasErrors();

        $review->refresh();
        $this->assertNotSame('submitted', $review->status);
        $this->assertSame($this->words(20), collect($review->items)->firstWhere('key', $bodyKey)['new']);
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

        $this->post(route('profile-review.save', $review->token), ['gal' => ['g0' => ['path' => 'profile-reviews/999/other.png', 'caption' => 'x'], 'g1' => ['path' => $path, 'caption' => 'تخرجي']]]);
        $this->assertSame([['path' => $path, 'caption' => 'تخرجي', 'approved' => null]], $review->fresh()->gallery);
    }

    public function test_approved_answers_go_live_and_the_rest_does_not(): void
    {
        $review = $this->review();
        $id = $this->achiever->id;
        $disk = Storage::disk(ProfileReviewApplier::diskName());
        $portrait = "profile-reviews/{$review->id}/me.png";
        $photo = "profile-reviews/{$review->id}/grad.png";
        $disk->put($portrait, base64_decode(self::PNG));
        $disk->put($photo, base64_decode(self::PNG));

        $edits = [
            "achiever_{$id}_name" => 'علي محمد الحبسي',
            "achiever_{$id}_story" => 'تعديل لم يُعتمد',
            "achiever_{$id}_portrait" => $portrait,
            "achievement_{$this->achievement->id}_body" => "فقرة أولى\n\nفقرة <ثانية>",
            "achievement_{$this->achievement->id}_achieved_at" => '2011-05',
        ];
        $review->items = collect($review->items)->map(function (array $item) use ($edits) {
            $item['decision'] = array_key_exists($item['key'], $edits) ? 'edit' : 'ok';
            $item['new'] = $edits[$item['key']] ?? null;
            $item['approved'] = $item['decision'] === 'edit' ? ! str_ends_with($item['key'], '_story') : null;

            return $item;
        })->all();
        $review->journey = [...$review->journey, 'new' => [['year' => '2003', 'text' => 'البداية في نادي النصر'], ['year' => '2010', 'text' => 'الاحتراف في إنجلترا']], 'approved' => true];
        $review->socials = [...$review->socials, 'new' => ['x' => '@ali', 'instagram' => '', 'linkedin' => '', 'facebook' => '', 'snapchat' => ''], 'approved' => true];
        $review->videos = [...$review->videos, 'new' => [['title' => 'لقاء', 'url' => 'https://youtu.be/abcdefghijk']], 'approved' => true];
        $review->contact = [...$review->contact, 'new' => ['phone' => '+968 9000 0000', 'email' => 'ali@example.com'], 'approved' => false];
        $review->gallery = [['path' => $photo, 'caption' => 'تخرجي', 'approved' => true]];
        $review->additions = [
            ['uid' => 'n1', 'title' => 'ذهبية آسيا', 'when' => '2024', 'description' => 'وصف', 'approved' => true],
            ['uid' => 'n2', 'title' => 'لم تُعتمد', 'when' => '2023', 'approved' => false],
        ];
        $review->save();

        $result = ProfileReviewApplier::apply($review->fresh());

        $this->assertSame(['changes' => 7, 'additions' => 1, 'photos' => 1], $result);
        $achiever = $this->achiever->fresh();
        $this->assertSame('علي محمد الحبسي', $achiever->getTranslation('name', 'ar'));
        $this->assertSame('بدأت الحكاية في الحارة.', $achiever->getTranslation('story', 'ar'));
        $this->assertSame([['2003', 'البداية في نادي النصر', 'ball'], ['2010', 'الاحتراف في إنجلترا', 'star']],
            $achiever->journeyItems->map(fn ($j) => [$j->year, $j->getTranslation('title', 'ar'), $j->icon])->all());
        $this->assertSame(['x'], $achiever->socialLinks->pluck('platform')->all());
        $this->assertSame('https://x.com/ali', $achiever->socialLinks->first()->url);
        $this->assertEquals([['title' => 'لقاء', 'url' => 'https://youtu.be/abcdefghijk']], $achiever->interviews);
        $this->assertNull($achiever->contact_phone);
        $this->assertNotNull($achiever->getFirstMedia('portrait'));
        $this->assertSame(['ar' => 'تخرجي'], $achiever->getFirstMedia('gallery')->getCustomProperty('caption'));

        $achievement = $this->achievement->fresh();
        $this->assertSame('<p>فقرة أولى</p><p>فقرة &lt;ثانية&gt;</p>', $achievement->getTranslation('body', 'ar'));
        $this->assertSame(['2011-05-01', 'month'], [$achievement->achieved_at->format('Y-m-d'), $achievement->date_precision]);

        $added = $achiever->achievements()->where('title->ar', 'ذهبية آسيا')->first();
        $this->assertNotNull($added);
        $this->assertTrue($added->isLive());
        $this->assertFalse(Achievement::query()->where('title->ar', 'لم تُعتمد')->exists());
    }
}
