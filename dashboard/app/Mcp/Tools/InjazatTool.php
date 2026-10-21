<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Achievement;
use App\Models\PublicFigure;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Tool;
use RuntimeException;

/**
 * Shared MCP authorization and output helpers for the Injazat content tools.
 */
abstract class InjazatTool extends Tool
{
    protected function user(): User
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            throw new RuntimeException('MCP tools require an authenticated user.');
        }

        return $user;
    }

    protected function can(string $ability): bool
    {
        return $this->user()->can($ability);
    }

    protected function limit(Request $request, int $default, int $max): int
    {
        return min($max, max(1, $request->integer('limit', $default)));
    }

    /** @param  array<string, mixed>  $content */
    protected function structured(array $content): ResponseFactory
    {
        return Response::structured($content);
    }

    protected function findAchievement(string $identifier): ?Achievement
    {
        return Achievement::query()
            ->where('slug', $identifier)
            ->when(Str::isUuid($identifier), fn ($query) => $query->orWhere('id', $identifier))
            ->first();
    }

    protected function findAchiever(string $identifier): ?PublicFigure
    {
        return PublicFigure::query()
            ->where('slug', $identifier)
            ->when(Str::isUuid($identifier), fn ($query) => $query->orWhere('id', $identifier))
            ->first();
    }

    /** Resolve a taxonomy slug to its id (existence is validated by the tool). */
    protected function taxonomyId(string $table, ?string $slug): ?string
    {
        if ($slug === null) {
            return null;
        }

        return (string) DB::table($table)->where('slug', $slug)->value('id');
    }

    /**
     * Slug from the request, or derived from the title with a uniqid fallback
     * (Arabic titles transliterate to nothing). Provided slugs are unique-validated
     * by the tool; auto-generated ones get a suffix on collision.
     */
    protected function uniqueSlug(?string $slug, ?string $titleEn, string $titleAr, string $table): string
    {
        if (is_string($slug) && $slug !== '') {
            return $slug;
        }

        $base = Str::slug((string) $titleEn) ?: Str::slug($titleAr) ?: 'entry';
        $candidate = $base;

        while (DB::table($table)->where('slug', $candidate)->exists()) {
            $candidate = $base.'-'.uniqid();
        }

        return $candidate;
    }

    /**
     * Resolve slug arrays to id => pivot-attributes maps for sync(); the pivot
     * models need an explicit UUID id.
     *
     * @param  list<string>  $slugs
     * @param  class-string  $model
     * @return array<string, array<string, string>>
     */
    protected function pivotSyncData(array $slugs, string $model): array
    {
        return $model::query()
            ->whereIn('slug', $slugs)
            ->pluck('id')
            ->mapWithKeys(fn (string $id): array => [$id => ['id' => (string) Str::uuid()]])
            ->all();
    }

    protected function achievementPayload(Achievement $achievement): array
    {
        $achievement->loadMissing(['achievementScope', 'achievementMainType', 'subAchievementType', 'publicFigures', 'organizations']);

        return [
            'id' => $achievement->id,
            'slug' => $achievement->slug,
            'title_ar' => $achievement->title_ar,
            'title_en' => $achievement->title_en,
            'excerpt_ar' => $achievement->excerpt_ar,
            'excerpt_en' => $achievement->excerpt_en,
            'description_ar' => $achievement->description_ar,
            'description_en' => $achievement->description_en,
            'scope' => $this->taxonomy($achievement->achievementScope),
            'main_type' => $this->taxonomy($achievement->achievementMainType),
            'sub_type' => $this->taxonomy($achievement->subAchievementType),
            'country_ar' => $achievement->country_ar,
            'country_en' => $achievement->country_en,
            'video_url' => $achievement->video_url,
            'achieved_at' => $achievement->achieved_at?->toDateString(),
            'is_published' => $achievement->is_published,
            'sort_order' => $achievement->sort_order,
            'achievers' => $achievement->publicFigures->pluck('slug')->all(),
            'organizations' => $achievement->organizations->pluck('slug')->all(),
        ];
    }

    protected function achieverPayload(PublicFigure $figure): array
    {
        $figure->loadMissing(['achievementMainType', 'achievements']);

        return [
            'id' => $figure->id,
            'slug' => $figure->slug,
            'name_ar' => $figure->name_ar,
            'name_en' => $figure->name_en,
            'headline_ar' => $figure->headline_ar,
            'headline_en' => $figure->headline_en,
            'description_ar' => $figure->description_ar,
            'description_en' => $figure->description_en,
            'story_ar' => $figure->story_ar,
            'story_en' => $figure->story_en,
            'main_type' => $this->taxonomy($figure->achievementMainType),
            'is_featured' => $figure->is_featured,
            'in_home_carousel' => $figure->in_home_carousel,
            'is_published' => $figure->is_published,
            'sort_order' => $figure->sort_order,
            'linkedin_url' => $figure->linkedin_url,
            'twitter_url' => $figure->twitter_url,
            'instagram_url' => $figure->instagram_url,
            'facebook_url' => $figure->facebook_url,
            'whatsapp_number' => $figure->whatsapp_number,
            'email' => $figure->email,
            'phone' => $figure->phone,
            'achievements' => $figure->achievements->pluck('slug')->all(),
        ];
    }

    private function taxonomy(mixed $model): ?array
    {
        if ($model === null) {
            return null;
        }

        return [
            'id' => $model->id,
            'slug' => $model->slug,
            'title_ar' => $model->title_ar,
            'title_en' => $model->title_en,
        ];
    }

    /** Escape LIKE metacharacters so user input remains a literal substring. */
    protected function like(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
