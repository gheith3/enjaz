<?php

namespace App\Support\ProfileReviews;

use App\Models\Achievement;
use App\Models\Achiever;
use App\Models\ProfileReview;
use App\Models\SocialLink;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\ResponseCache\Facades\ResponseCache;

/**
 * Applies the corrections and additions the team approved (approved = true) to the live records.
 * Records save through their models, so the public pages' cache is cleared and the activity log keeps who changed what.
 */
class ProfileReviewApplier
{
    /** Social platform => pattern of its hosts. */
    private const PLATFORM_HOSTS = [
        'instagram' => '/(^|\.)instagram\.com$/', 'x' => '/(^|\.)(x|twitter)\.com$/', 'facebook' => '/(^|\.)(facebook\.com|fb\.com|fb\.me)$/',
        'linkedin' => '/(^|\.)linkedin\.com$/', 'youtube' => '/(^|\.)(youtube\.com|youtu\.be)$/', 'snapchat' => '/(^|\.)snapchat\.com$/',
        'tiktok' => '/(^|\.)tiktok\.com$/', 'whatsapp' => '/(^|\.)(wa\.me|whatsapp\.com)$/',
    ];

    /** @return array{changes: int, additions: int} */
    public static function apply(ProfileReview $review): array
    {
        $changes = 0;
        $additions = 0;

        DB::transaction(function () use ($review, &$changes, &$additions) {
            foreach ($review->items ?? [] as $item) {
                if (($item['decision'] ?? null) === 'edit' && ($item['approved'] ?? false) === true && self::applyItem($item)) {
                    $changes++;
                }
            }

            foreach ($review->additions ?? [] as $addition) {
                if (($addition['approved'] ?? false) === true && filled($addition['title'] ?? null)) {
                    self::createAchievement($review->achiever, $addition);
                    $additions++;
                }
            }
        });

        if (config('responsecache.enabled')) {
            ResponseCache::clear();
        }

        return compact('changes', 'additions');
    }

    /** @param  array<string, mixed>  $item */
    private static function applyItem(array $item): bool
    {
        $class = ProfileReviewItems::MODELS[$item['model'] ?? ''] ?? null;
        $model = $class ? $class::query()->find($item['id'] ?? 0) : null;
        if (! $model) {
            return false;
        }

        $attr = $item['attr'];
        $value = is_string($item['new'] ?? null) ? trim($item['new']) : ($item['new'] ?? null);

        switch ($item['type']) {
            case 'image':
                if (! filled($value) || ! self::disk()->exists($value)) {
                    return false;
                }
                $model->addMediaFromDisk($value, self::diskName())->toMediaCollection($attr);
                $model->touch();

                return true;
            case 'journey':
                self::replaceJourney($model, (string) $value);

                return true;
            case 'links':
                self::replaceLinks($model, (string) $value);

                return true;
            case 'select':
                $model->{$attr} = filled($value) ? (int) $value : null;
                break;
            case 'when':
                [$date, $precision] = self::parseWhen((string) $value);
                if (! $date) {
                    return false;
                }
                $model->achieved_at = $date;
                $model->date_precision = $precision;
                break;
            case 'html':
                $model->setTranslation($attr, 'ar', self::paragraphs((string) $value));
                break;
            default:
                $model->setTranslation($attr, 'ar', (string) $value);
        }

        $model->save();

        return true;
    }

    /** @param  array<string, mixed>  $addition */
    private static function createAchievement(Achiever $achiever, array $addition): Achievement
    {
        [$date, $precision] = self::parseWhen((string) ($addition['when'] ?? ''));
        $description = trim((string) ($addition['description'] ?? ''));
        $translated = fn (?string $value) => filled($value) ? ['ar' => trim($value)] : null;

        $achievement = Achievement::create([
            'slug' => self::uniqueSlug($achiever),
            'title' => ['ar' => trim($addition['title'])],
            'excerpt' => $translated(Str::limit(preg_replace('/\s+/u', ' ', $description), 220)),
            'body' => $translated(self::paragraphs($description)),
            'scope_id' => filled($addition['scope_id'] ?? null) ? (int) $addition['scope_id'] : null,
            'field_id' => $achiever->field_id,
            'discipline' => $translated($addition['discipline'] ?? null),
            'issuer' => $translated($addition['issuer'] ?? null),
            'country' => $translated($addition['country'] ?? null),
            'achieved_at' => $date,
            'date_precision' => $date ? $precision : 'day',
            'is_published' => true,
            'published_at' => now(),
        ]);
        $achievement->achievers()->attach($achiever->id, ['is_primary' => true, 'sort_order' => 0]);

        $cover = $addition['cover'] ?? null;
        if (filled($cover) && self::disk()->exists($cover)) {
            $achievement->addMediaFromDisk($cover, self::diskName())->toMediaCollection('cover');
        }

        return $achievement;
    }

    private static function replaceJourney(Achiever $achiever, string $value): void
    {
        $icons = $achiever->journeyItems()->pluck('icon')->all();
        $achiever->journeyItems()->delete();

        foreach (self::lines($value) as $index => $line) {
            $parts = str_contains($line, '|')
                ? preg_split('/\s*\|\s*/u', $line, 2)
                : preg_split('/\s+[-–—:]\s+|\s*[،:]\s*/u', $line, 2);
            [$year, $title] = count($parts) === 2 && preg_match('/^[\d٠-٩]{2,4}/u', $parts[0]) ? $parts : ['', $line];
            $achiever->journeyItems()->create([
                'year' => Str::limit(self::latinDigits($year), 20, ''),
                'title' => ['ar' => $title],
                'icon' => $icons[$index] ?? 'star',
                'sort_order' => $index + 1,
            ]);
        }
        $achiever->touch();
    }

    private static function replaceLinks(Achiever $achiever, string $value): void
    {
        $achiever->socialLinks()->delete();

        foreach (self::lines($value) as $index => $url) {
            $url = Str::startsWith($url, ['http://', 'https://']) ? $url : 'https://'.ltrim($url, '/');
            $host = Str::lower((string) parse_url($url, PHP_URL_HOST));
            $platform = collect(self::PLATFORM_HOSTS)->filter(fn ($pattern) => preg_match($pattern, $host))->keys()->first() ?? 'website';
            $achiever->socialLinks()->create([
                'platform' => $platform,
                'url' => Str::limit($url, 250, ''),
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
        }
        $achiever->touch();
    }

    /**
     * "2019", "2019-04" or "2019-04-29" => [date, precision].
     *
     * @return array{0: ?Carbon, 1: string}
     */
    public static function parseWhen(string $value): array
    {
        if (! preg_match('/^(\d{4})(?:-(\d{1,2}))?(?:-(\d{1,2}))?$/', trim($value), $m)) {
            return [null, 'day'];
        }
        $month = (int) ($m[2] ?? 0);
        $day = (int) ($m[3] ?? 0);
        $precision = $month && $day ? 'day' : ($month ? 'month' : 'year');

        if ($month && $day && ! checkdate($month, $day, (int) $m[1])) {
            return [null, 'day'];
        }

        return [Carbon::create((int) $m[1], $month ?: 1, $day ?: 1)->startOfDay(), $precision];
    }

    /** Plain text with blank lines between paragraphs => <p> paragraphs. */
    public static function paragraphs(string $text): string
    {
        return collect(preg_split('/\R\s*\R/u', trim($text)))
            ->map(fn ($paragraph) => trim($paragraph))->filter()
            ->map(fn ($paragraph) => '<p>'.nl2br(e($paragraph), false).'</p>')
            ->implode('');
    }

    /** @return list<string> */
    private static function lines(string $value): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/u', $value))));
    }

    private static function latinDigits(string $value): string
    {
        return strtr(trim($value), ['٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9']);
    }

    private static function uniqueSlug(Achiever $achiever): string
    {
        do {
            $slug = Str::limit($achiever->slug, 60, '').'-'.Str::lower(Str::random(6));
        } while (Achievement::withTrashed()->where('slug', $slug)->exists());

        return $slug;
    }

    public static function diskName(): string
    {
        return (string) config('media-library.disk_name');
    }

    public static function disk(): Filesystem
    {
        return Storage::disk(self::diskName());
    }
}
