<?php

namespace App\Support\ProfileReviews;

use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\Field;
use App\Models\Quote;
use App\Models\Scope;
use App\Models\SocialLink;
use App\Support\ArabicDate;
use Illuminate\Database\Eloquent\Model;

/**
 * The points of an achiever's profile that the achiever reviews, one entry per point:
 *
 *   key        unique, [a-z0-9_] (form field names)
 *   section    group on the review page (about, story, images, journey, links, quotes, achievement_{id})
 *   title      section title (achievement title for achievement sections)
 *   label      the point ("الاسم")
 *   type       text | textarea | html | select | when | image | journey | links
 *   model, id, attr   what an approved answer changes (attr = media collection for images)
 *   old        current value as the input expects it (image: URL)
 *   shown      current value as the achiever reads it (date in words, option label ...)
 *   options    select options [id => label]
 *   decision   null | ok | edit (the achiever's answer)
 *   new        the corrected value (image: path of the uploaded file on the media disk)
 *   approved   null | bool (the team's decision on a correction)
 */
class ProfileReviewItems
{
    /** Models an item may change. */
    public const MODELS = ['achiever' => Achiever::class, 'achievement' => Achievement::class, 'quote' => Quote::class];

    /** Images the achiever may replace (collection => label, hint). */
    public const IMAGES = [
        'portrait' => ['الصورة الرئيسية', 'صورة شخصية واضحة، يفضّل أن تكون مربعة وبجودة عالية.'],
        'cutout' => ['صورة بخلفية مفرّغة', 'صورة لك من الرأس حتى الخصر تقريبًا، يفضّل PNG بخلفية شفافة، وإن لم تتوفر أرسل صورة واضحة بخلفية سادة وسنتولى تفريغها.'],
        'avatar' => ['صورة صغيرة دائرية', 'صورة قريبة للوجه تُعرض داخل دائرة صغيرة.'],
        'journey' => ['صورة قسم «مسيرتي»', 'صورة تعبّر عن رحلتك (في الملعب، على المنصة، أثناء العمل ...).'],
    ];

    /**
     * @return list<array<string, mixed>>
     */
    public static function build(Achiever $achiever): array
    {
        $achiever->loadMissing(['field', 'journeyItems', 'quotes', 'socialLinks', 'media', 'achievements' => fn ($q) => $q->with(['scope', 'media'])->orderByDesc('achieved_at')]);
        $items = [];

        $about = ['about', 'معلوماتك الأساسية'];
        $items[] = self::text($achiever, 'name', 'الاسم الكامل', $about);
        $items[] = self::text($achiever, 'headline', 'الوصف المختصر تحت الاسم', $about, hint: 'مثل: حارس مرمى المنتخب الوطني');
        $items[] = self::item($about, 'achiever', $achiever->id, 'field_id', 'المجال', 'select', $achiever->field_id,
            $achiever->field?->name, options: self::fieldOptions());
        $items[] = self::text($achiever, 'hero_lines', 'أسطر التعريف أعلى صفحتك', $about, 'textarea', 'سطر لكل معلومة، مثل: حارس مرمى سابق للمنتخب الوطني');

        $story = ['story', 'نبذتك وقصتك'];
        $items[] = self::text($achiever, 'short_bio', 'النبذة المختصرة', $story, 'textarea');
        $items[] = self::text($achiever, 'story', 'قصتك', $story, 'textarea', 'اترك سطرًا فارغًا بين كل فقرة وأخرى.');
        $items[] = self::text($achiever, 'personal_quote', 'مقولتك الشخصية', $story, 'textarea', 'عبارة تؤمن بها أو قلتها في مناسبة.');
        $items[] = self::text($achiever, 'personal_quote_context', 'مناسبة المقولة وتاريخها', $story);
        $items[] = self::text($achiever, 'personal_quote_source', 'مصدر المقولة', $story);

        $images = ['images', 'صورك'];
        foreach (self::IMAGES as $collection => [$label, $hint]) {
            $items[] = self::item($images, 'achiever', $achiever->id, $collection, $label, 'image',
                $achiever->imageUrl($collection, 'card'), null, $hint);
        }

        $journey = $achiever->journeyItems->map(fn ($item) => trim($item->year.' | '.$item->getTranslation('title', 'ar', false)))->implode("\n");
        $items[] = self::item(['journey', 'محطات مسيرتك'], 'achiever', $achiever->id, 'journey_items', 'محطات المسيرة', 'journey',
            $journey, null, 'سطر لكل محطة: السنة ثم علامة | ثم المحطة. مثال: 2019 | الانضمام إلى المنتخب الوطني');

        $links = $achiever->socialLinks->filter(fn (SocialLink $link) => filled($link->url))->pluck('url')->implode("\n");
        $items[] = self::item(['links', 'حساباتك على مواقع التواصل'], 'achiever', $achiever->id, 'social_links', 'روابط الحسابات', 'links',
            $links, null, 'رابط واحد في كل سطر (إنستغرام، إكس، لينكدإن، يوتيوب، موقعك ...).');

        foreach ($achiever->quotes as $index => $quote) {
            $section = ['quotes', 'قالوا عنك'];
            $n = $index + 1;
            $items[] = self::text($quote, 'text', "القول $n", $section, 'textarea');
            $items[] = self::text($quote, 'name', "قائل القول $n", $section);
            $items[] = self::text($quote, 'role', "صفة القائل $n", $section);
        }

        foreach ($achiever->achievements as $achievement) {
            $section = ['achievement_'.$achievement->id, $achievement->getTranslation('title', 'ar', false)];
            $body = trim(preg_replace("/\n{3,}/", "\n\n", html_entity_decode(strip_tags(
                preg_replace('#</(p|h[2-4]|li|blockquote)>#i', "\n\n", (string) $achievement->getTranslation('body', 'ar', false))
            ))));
            $items[] = self::text($achievement, 'title', 'عنوان الإنجاز', $section);
            $items[] = self::item($section, 'achievement', $achievement->id, 'achieved_at', 'تاريخ الإنجاز', 'when',
                self::whenValue($achievement), ArabicDate::format($achievement->achieved_at, $achievement->date_precision));
            $items[] = self::item($section, 'achievement', $achievement->id, 'scope_id', 'مستوى الإنجاز', 'select', $achievement->scope_id,
                $achievement->scope?->name, options: self::scopeOptions());
            $items[] = self::text($achievement, 'discipline', 'التخصص أو الرياضة أو الفئة', $section);
            $items[] = self::text($achievement, 'issuer', 'الجهة المانحة أو المنظِّمة', $section);
            $items[] = self::text($achievement, 'country', 'المكان (الدولة أو المدينة)', $section);
            $items[] = self::text($achievement, 'excerpt', 'وصف مختصر للإنجاز', $section, 'textarea');
            $items[] = self::item($section, 'achievement', $achievement->id, 'body', 'قصة الإنجاز', 'html', $body, null,
                'اترك سطرًا فارغًا بين كل فقرة وأخرى.');
            $items[] = self::item($section, 'achievement', $achievement->id, 'cover', 'صورة الإنجاز', 'image',
                $achievement->imageUrl('cover', 'card'), null, 'صورة من لحظة الإنجاز أو التتويج، أفقية إن أمكن.');
        }

        return $items;
    }

    /** "2019" / "2019-04" / "2019-04-29" for the year / month / day inputs. */
    public static function whenValue(Achievement $achievement): string
    {
        return match (true) {
            $achievement->achieved_at === null => '',
            $achievement->date_precision === 'year' => $achievement->achieved_at->format('Y'),
            $achievement->date_precision === 'month' => $achievement->achieved_at->format('Y-m'),
            default => $achievement->achieved_at->format('Y-m-d'),
        };
    }

    /** @return array<int, string> */
    public static function fieldOptions(): array
    {
        return Field::query()->where('is_active', true)->orderBy('sort_order')->get()
            ->mapWithKeys(fn (Field $field) => [$field->id => $field->getTranslation('name', 'ar', false)])->all();
    }

    /** @return array<int, string> */
    public static function scopeOptions(): array
    {
        return Scope::query()->orderBy('sort_order')->get()
            ->mapWithKeys(fn (Scope $scope) => [$scope->id => $scope->getTranslation('name', 'ar', false)])->all();
    }

    /**
     * A translatable text attribute (Arabic).
     *
     * @param  array{0: string, 1: string}  $section
     */
    private static function text(Model $model, string $attr, string $label, array $section, string $type = 'text', ?string $hint = null): array
    {
        $old = (string) $model->getTranslation($attr, 'ar', false);

        return self::item($section, array_search($model::class, self::MODELS, true), $model->getKey(), $attr, $label, $type, $old, null, $hint);
    }

    /**
     * @param  array{0: string, 1: string}  $section
     * @param  array<int|string, string>|null  $options
     */
    private static function item(array $section, string $model, int $id, string $attr, string $label, string $type, mixed $old,
        ?string $shown = null, ?string $hint = null, ?array $options = null): array
    {
        return array_filter([
            'key' => "{$model}_{$id}_{$attr}",
            'section' => $section[0],
            'title' => $section[1],
            'label' => $label,
            'hint' => $hint,
            'type' => $type,
            'model' => $model,
            'id' => $id,
            'attr' => $attr,
            'old' => $old,
            'shown' => $shown,
            'options' => $options,
        ], fn ($value) => $value !== null) + ['decision' => null, 'new' => null, 'approved' => null];
    }
}
