<?php

namespace App\Support\ProfileReviews;

use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\Field;
use App\Models\Scope;
use App\Models\SocialLink;
use App\Support\ArabicDate;
use Illuminate\Database\Eloquent\Model;

/**
 * What an achiever reviews (تصميم المراجعة ٢: «ملف توثيق»).
 *
 * items: one entry per existing point; the achiever leaves it as it is or presses «تعديل»:
 *   key        unique, [a-z0-9_] (form field names)
 *   section    group on the page (about, story, images, achievement_{id})
 *   title      section title (the achievement's title for achievement sections)
 *   label, hint, type (text | textarea | html | select | scope | when | image)
 *   model, id, attr   what an approved answer changes (attr = media collection for images)
 *   old, shown, options, min_words
 *   decision   ok | edit,  new: the corrected value,  approved: the team's decision
 *
 * The other parts of the page are lists (snapshots kept on the review, see sections()): «محطات مسيرتك»,
 * the social accounts, the interviews and the private contact details; plus new photos and new achievements.
 */
class ProfileReviewItems
{
    /** Models an item may change. */
    public const MODELS = ['achiever' => Achiever::class, 'achievement' => Achievement::class];

    /** Social accounts asked for, in this order (platform => label). */
    public const SOCIALS = ['x' => 'إكس (تويتر)', 'instagram' => 'إنستغرام', 'linkedin' => 'لينكدإن', 'facebook' => 'فيسبوك', 'snapchat' => 'سناب شات'];

    public const MAX_STATIONS = 10;

    public const STATION_WORDS = 8;

    public const MIN_WORDS = 120;

    /**
     * @return list<array<string, mixed>>
     */
    public static function build(Achiever $achiever): array
    {
        $achiever->loadMissing(['field', 'media', 'achievements' => fn ($q) => $q->with(['scope', 'media'])->orderByDesc('achieved_at')]);
        $items = [];

        $about = ['about', 'معلوماتك الأساسية'];
        $items[] = self::text($achiever, 'name', 'الاسم الكامل', $about);
        $items[] = self::text($achiever, 'headline', 'الوصف المختصر تحت الاسم', $about, hint: 'مثل: روائية وأستاذة جامعية');
        $items[] = self::item($about, 'achiever', $achiever->id, 'field_id', 'المجال', 'select', $achiever->field_id,
            $achiever->field?->name, options: self::fieldOptions());

        $story = ['story', 'نبذتك وقصتك'];
        $items[] = self::text($achiever, 'short_bio', 'النبذة المختصرة', $story, 'textarea');
        $items[] = self::text($achiever, 'story', 'قصتك', $story, 'textarea', 'اترك سطرًا فارغًا بين كل فقرة وأخرى.');

        $items[] = self::item(['images', 'صورتك'], 'achiever', $achiever->id, 'portrait', 'الصورة الرئيسية', 'image',
            $achiever->imageUrl('portrait', 'card'), null, 'صورة شخصية واضحة وبجودة عالية.');

        foreach ($achiever->achievements as $achievement) {
            $section = ['achievement_'.$achievement->id, (string) $achievement->getTranslation('title', 'ar', false)];
            $items[] = self::text($achievement, 'title', 'عنوان الإنجاز', $section);
            $items[] = self::item($section, 'achievement', $achievement->id, 'achieved_at', 'التاريخ', 'when',
                self::whenValue($achievement), ArabicDate::format($achievement->achieved_at, $achievement->date_precision));
            $items[] = self::item($section, 'achievement', $achievement->id, 'scope_id', 'المستوى', 'scope', $achievement->scope_id,
                $achievement->scope?->name, options: self::scopeOptions());
            $items[] = self::text($achievement, 'discipline', 'التخصص أو الرياضة أو الفئة', $section);
            $items[] = self::text($achievement, 'country', 'المكان أو الدولة', $section);
            $items[] = self::item($section, 'achievement', $achievement->id, 'body', 'وصف الإنجاز وتفاصيله', 'html', self::plain($achievement), null,
                'اذكر تفاصيل الإنجاز: ما هو، ومتى وأين، ومن منحه، ولماذا هو مهم. لا يقل عن '.ArabicDate::digits(self::MIN_WORDS).' كلمة.')
                + ['min_words' => self::MIN_WORDS];
            $items[] = self::item($section, 'achievement', $achievement->id, 'cover', 'صورة الإنجاز', 'image', $achievement->imageUrl('cover', 'card'));
        }

        return $items;
    }

    /**
     * The list parts of the review, with what the profile has now ("old"); the achiever's answer goes in "new".
     *
     * @return array{journey: array<string, mixed>, socials: array<string, mixed>, videos: array<string, mixed>, contact: array<string, mixed>, gallery: list<array<string, mixed>>}
     */
    public static function sections(Achiever $achiever): array
    {
        $achiever->loadMissing(['journeyItems', 'socialLinks']);
        $journey = $achiever->journeyItems->take(self::MAX_STATIONS)
            ->map(fn ($item) => ['year' => (string) $item->year, 'text' => (string) $item->getTranslation('title', 'ar', false)])->values()->all();
        $socials = collect(self::SOCIALS)->map(fn ($label, $platform) => (string) ($achiever->socialLinks
            ->first(fn (SocialLink $link) => $link->platform === $platform && filled($link->url))?->url ?? ''))->all();
        $videos = collect($achiever->interviews ?? [])->map(fn ($v) => ['title' => (string) ($v['title'] ?? ''), 'url' => (string) ($v['url'] ?? '')])->values()->all();
        $contact = ['phone' => (string) $achiever->contact_phone, 'email' => (string) $achiever->contact_email];

        return [
            'journey' => ['old' => $journey, 'new' => $journey, 'approved' => null],
            'socials' => ['old' => $socials, 'new' => $socials, 'approved' => null],
            'videos' => ['old' => $videos, 'new' => $videos, 'approved' => null],
            'contact' => ['old' => $contact, 'new' => $contact, 'approved' => null],
            'gallery' => [],
        ];
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

    public static function words(?string $text): int
    {
        return count(preg_split('/\s+/u', trim((string) $text), -1, PREG_SPLIT_NO_EMPTY));
    }

    /** An achievement's story as plain text, a blank line between paragraphs. */
    private static function plain(Achievement $achievement): string
    {
        $html = (string) $achievement->getTranslation('body', 'ar', false);

        return trim(preg_replace("/\n{3,}/", "\n\n", html_entity_decode(strip_tags(
            preg_replace('#</(p|h[2-4]|li|blockquote)>#i', "\n\n", $html)
        ))));
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
        ], fn ($value) => $value !== null) + ['decision' => 'ok', 'new' => null, 'approved' => null];
    }
}
