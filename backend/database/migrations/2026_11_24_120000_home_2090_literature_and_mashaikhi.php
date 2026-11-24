<?php

use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\Field;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Scope;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;

/*
 * تصاميم ٢٠٩٠ (approved), home page content:
 * - «منجزو الثقافة» becomes «الأدب» with four literature achievers (their profile photo, a badge each).
 * - «الرياضة»: Mohammed Al-Mashaikhi as the fifth person (cut-out photo, database/data/home-2090), in place of the repeated card.
 * - Mohammed Al-Mashaikhi: field «الرياضة» and his Tokyo 2020 Paralympic bronze in shot put (10.84 m, Omani record,
 *   first Paralympic medal for Oman; Oman Daily, omandaily.om/ampArticle/1006429 and /1006177).
 * Records only filled when missing; the dashboard owns them afterwards.
 */
return new class extends Migration
{
    private const LITERATURE = [
        ['jokha-alharthi', 'الرواية'],
        ['zahran-alqasmi', 'الرواية والشعر'],
        ['bushra-khalfan', 'القصة القصيرة'],
        ['saif-alrahbi', 'الشعر'],
    ];

    public function up(): void
    {
        $home = Page::query()->where('key', 'home')->first();
        $mashaikhi = Achiever::query()->where('slug', 'mohammed-almashaikhi')->first();

        if ($mashaikhi) {
            $this->mashaikhi($mashaikhi);
        }
        if (! $home) {
            return;
        }

        $culture = $home->sections()->where('key', 'home.culture')->first();
        if ($culture) {
            $this->literature($culture);
        }

        $sports = $home->sections()->where('key', 'home.sports')->first();
        if ($sports && $mashaikhi && ! $sports->items()->where('itemable_type', $mashaikhi->getMorphClass())->where('itemable_id', $mashaikhi->id)->exists()) {
            $item = $sports->items()->create([
                'itemable_type' => $mashaikhi->getMorphClass(),
                'itemable_id' => $mashaikhi->id,
                'title' => ['ar' => 'محمد المشايخي'],
                'label' => ['ar' => 'ألعاب القوى البارالمبية'],
                'sort_order' => (int) $sports->items()->max('sort_order') + 1,
            ]);
            $this->attach($item, 'image', database_path('data/home-2090/mashaikhi-cutout.png'));
        }
    }

    private function literature(PageSection $section): void
    {
        $settings = $section->settings ?? [];
        $settings['title'] = ['ar' => 'الأدب', 'en' => 'Literature'];
        $settings['lead'] = ['ar' => 'أقلام عُمانية وصلت إلى العالم', 'en' => 'Omani writers who reached the world'];
        $section->update(['settings' => $settings]);

        $people = collect(self::LITERATURE)
            ->map(fn ($row) => [Achiever::query()->where('slug', $row[0])->first(), $row[1]])
            ->filter(fn ($row) => $row[0] !== null);
        if ($people->isEmpty()) {
            return;
        }

        $section->items()->get()->each->delete();
        $people->values()->each(function (array $row, int $i) use ($section) {
            [$achiever, $badge] = $row;
            $item = $section->items()->create([
                'itemable_type' => $achiever->getMorphClass(),
                'itemable_id' => $achiever->id,
                'label' => ['ar' => $badge],
                'sort_order' => $i + 1,
            ]);
            $photo = $achiever->getFirstMedia('portrait');
            if ($photo) {
                try {
                    $photo->copy($item, 'image');
                } catch (Throwable $e) {
                    Log::warning("Home literature photo not copied ({$achiever->slug}): ".$e->getMessage());
                }
            }
        });
    }

    private function mashaikhi(Achiever $achiever): void
    {
        $sports = Field::query()->where('slug', 'sports')->first();
        if ($sports && ! $achiever->field_id) {
            $achiever->update(['field_id' => $sports->id]);
        }
        if (blank($achiever->getTranslation('headline', 'ar', false))) {
            $achiever->setTranslation('headline', 'ar', 'ألعاب القوى البارالمبية · دفع الجلة');
            $achiever->save();
        }
        if ($achiever->achievements()->exists()) {
            return;
        }

        $achievement = Achievement::create([
            'slug' => 'mashaikhi-tokyo-2020-paralympic-bronze',
            'title' => ['ar' => 'برونزية البارالمبياد - طوكيو'],
            'excerpt' => ['ar' => 'الميدالية البرونزية في دفع الجلة بدورة الألعاب البارالمبية طوكيو ٢٠٢٠، برمية ١٠٫٨٤ متر حطّمت الرقم العُماني، وهي أول ميدالية بارالمبية لسلطنة عُمان.'],
            'body' => ['ar' => '<p>حقق محمد بن جميل المشايخي الميدالية البرونزية في دفع الجلة ضمن دورة الألعاب البارالمبية طوكيو ٢٠٢٠، التي أُقيمت بين ٢٤ أغسطس و٥ سبتمبر ٢٠٢١، برمية بلغت ١٠٫٨٤ متر حطّم بها الرقم القياسي العُماني.</p><p>وهي أول ميدالية بارالمبية تحرزها سلطنة عُمان، واحتفت بها سفارة السلطنة في طوكيو، وأشاد بها مجلس إدارة اللجنة الأولمبية العُمانية بوصفها إنجازًا تاريخيًا.</p>'],
            'scope_id' => Scope::query()->where('slug', 'international')->value('id'),
            'field_id' => $sports?->id,
            'discipline' => ['ar' => 'دفع الجلة (ألعاب القوى البارالمبية)'],
            'country' => ['ar' => 'اليابان'],
            'achieved_at' => '2021-01-01',
            'date_precision' => 'year',
            'rank' => 'bronze',
            'is_published' => true,
            'published_at' => now(),
        ]);
        $achievement->achievers()->attach($achiever->id, ['is_primary' => true, 'sort_order' => 0]);
        $this->attach($achievement, 'cover', public_path('images/timeline/mashaikhi-wide.jpg'));
    }

    private function attach($model, string $collection, string $file): void
    {
        if (! is_file($file) || $model->hasMedia($collection)) {
            return;
        }
        try {
            $model->addMedia($file)->preservingOriginal()->toMediaCollection($collection);
        } catch (Throwable $e) {
            Log::warning("Home 2090 photo not attached ({$collection}): ".$e->getMessage());
        }
    }

    public function down(): void
    {
        // Data only; the dashboard owns these records afterwards.
    }
};
