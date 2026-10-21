<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\AchievementMainType;
use App\Models\AchievementScope;
use App\Models\Organization;
use App\Models\PublicFigure;
use App\Models\SubAchievementType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Imports owner-provided content from every JSON file in
 * database/seeders/ContentToImport/ (files ending in .example.json are
 * skipped). Each file may contain "achievers" and/or "achievements" arrays —
 * see achievers-achievements.example.json and README.md in that folder.
 *
 * Images referenced in the JSON are resolved relative to the ContentToImport
 * folder and copied into storage/app/public/content/. Idempotent: records
 * are upserted by slug.
 */
class ContentImportSeeder extends Seeder
{
    private const IMPORT_PATH = 'database/seeders/ContentToImport';

    public function run(): void
    {
        [$achievers, $achievements] = $this->loadJsonFiles();

        if ($achievers === [] && $achievements === []) {
            $this->command?->info('No content to import in '.self::IMPORT_PATH);

            return;
        }

        $achieverIds = $this->seedAchievers($achievers);
        $this->seedAchievements($achievements, $achieverIds);

        $this->linkAchieverAchievements($achievers, $achieverIds);
    }

    /** @return array{0: array, 1: array} merged achievers and achievements */
    private function loadJsonFiles(): array
    {
        $achievers = [];
        $achievements = [];

        $files = array_filter(
            File::glob(base_path(self::IMPORT_PATH.'/*.json')) ?: [],
            fn (string $file) => ! str_ends_with($file, '.example.json'),
        );

        foreach ($files as $file) {
            $data = json_decode(File::get($file), true, flags: JSON_THROW_ON_ERROR);

            $achievers = array_merge($achievers, $data['achievers'] ?? []);
            $achievements = array_merge($achievements, $data['achievements'] ?? []);

            $this->command?->info(sprintf(
                'Loaded %s: %d achievers, %d achievements',
                basename($file),
                count($data['achievers'] ?? []),
                count($data['achievements'] ?? []),
            ));
        }

        return [$achievers, $achievements];
    }

    /** @return array<string, string> slug => id */
    private function seedAchievers(array $achievers): array
    {
        $ids = [];

        foreach ($achievers as $item) {
            $mainTypeId = $this->lookupId(AchievementMainType::class, $item['main_type'] ?? null);

            $figure = PublicFigure::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name_ar' => $item['name_ar'],
                    'name_en' => $item['name_en'] ?? null,
                    'headline_ar' => $item['headline_ar'] ?? null,
                    'headline_en' => $item['headline_en'] ?? null,
                    'description_ar' => $item['description_ar'] ?? null,
                    'description_en' => $item['description_en'] ?? null,
                    'story_ar' => $item['story_ar'] ?? null,
                    'story_en' => $item['story_en'] ?? null,
                    'achievement_main_type_id' => $mainTypeId,
                    'image_path' => $this->importImage($item['images']['image'] ?? null),
                    'tile_image_path' => $this->importImage($item['images']['tile'] ?? null),
                    'cutout_image_path' => $this->importImage($item['images']['cutout'] ?? null),
                    'banner_image_path' => $this->importImage($item['images']['banner'] ?? null),
                    'is_featured' => $item['is_featured'] ?? false,
                    'in_home_carousel' => $item['in_home_carousel'] ?? false,
                    'is_published' => $item['is_published'] ?? true,
                    'sort_order' => $item['sort_order'] ?? 0,
                    'linkedin_url' => $item['social']['linkedin_url'] ?? null,
                    'twitter_url' => $item['social']['twitter_url'] ?? null,
                    'instagram_url' => $item['social']['instagram_url'] ?? null,
                    'facebook_url' => $item['social']['facebook_url'] ?? null,
                    'whatsapp_number' => $item['social']['whatsapp_number'] ?? null,
                    'email' => $item['social']['email'] ?? null,
                    'phone' => $item['social']['phone'] ?? null,
                ],
            );

            $ids[$item['slug']] = $figure->id;
        }

        $this->command?->info(sprintf('Achievers imported: %d', count($ids)));

        return $ids;
    }

    /** @param array<string, string> $achieverIds slug => id */
    private function seedAchievements(array $achievements, array $achieverIds): void
    {
        foreach ($achievements as $item) {
            $achievement = Achievement::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'title_ar' => $item['title_ar'],
                    'title_en' => $item['title_en'] ?? null,
                    'excerpt_ar' => $item['excerpt_ar'] ?? null,
                    'excerpt_en' => $item['excerpt_en'] ?? null,
                    'description_ar' => $item['description_ar'] ?? null,
                    'description_en' => $item['description_en'] ?? null,
                    'achievement_scope_id' => $this->lookupId(AchievementScope::class, $item['scope'] ?? null),
                    'achievement_main_type_id' => $this->lookupId(AchievementMainType::class, $item['main_type'] ?? null),
                    'sub_achievement_type_id' => $this->lookupId(SubAchievementType::class, $item['sub_type'] ?? null),
                    'achieved_at' => $item['achieved_at'] ?? null,
                    'country_ar' => $item['country_ar'] ?? null,
                    'country_en' => $item['country_en'] ?? null,
                    'cover_image_path' => $this->importImage($item['cover_image'] ?? null),
                    'video_url' => $item['video_url'] ?? null,
                    'is_published' => $item['is_published'] ?? true,
                    'sort_order' => $item['sort_order'] ?? 0,
                ],
            );

            $achievement->publicFigures()->sync($this->pivotIds($achieverIds, $item['achievers'] ?? []));
            $achievement->organizations()->sync(
                $this->pivotIds($this->organizationIds(), $item['organizations'] ?? []),
            );
        }

        $this->command?->info(sprintf('Achievements imported: %d', count($achievements)));
    }

    /** Links achievements declared on the achiever side of the JSON. */
    private function linkAchieverAchievements(array $achievers, array $achieverIds): void
    {
        foreach ($achievers as $item) {
            $achievementIds = Achievement::query()
                ->whereIn('slug', $item['achievements'] ?? [])
                ->pluck('id')
                ->all();

            if ($achievementIds === []) {
                continue;
            }

            PublicFigure::query()
                ->whereKey($achieverIds[$item['slug']])
                ->first()
                ->achievements()
                ->syncWithoutDetaching($this->uuidPivotAttributes($achievementIds));
        }
    }

    /** @return array<string, string> slug => id */
    private function organizationIds(): array
    {
        return Organization::query()->pluck('id', 'slug')->all();
    }

    private function lookupId(string $model, ?string $slug): ?string
    {
        if ($slug === null) {
            return null;
        }

        $id = $model::query()->where('slug', $slug)->value('id');

        if ($id === null) {
            $this->command?->warn(sprintf('Skipped unknown %s slug: %s', class_basename($model), $slug));
        }

        return $id;
    }

    /**
     * @param  array<string, string>  $slugToId
     * @param  array<string>  $slugs
     * @return array<string, array<string, string>>
     */
    private function pivotIds(array $slugToId, array $slugs): array
    {
        return $this->uuidPivotAttributes(
            array_values(array_intersect_key($slugToId, array_flip($slugs))),
        );
    }

    /**
     * @param  array<string>  $ids
     * @return array<string, array<string, string>>
     */
    private function uuidPivotAttributes(array $ids): array
    {
        return collect($ids)
            ->mapWithKeys(fn (string $id) => [$id => ['id' => (string) Str::uuid()]])
            ->all();
    }

    private function importImage(?string $path): ?string
    {
        if ($path === null) {
            return null;
        }

        $source = base_path(self::IMPORT_PATH.'/'.$path);

        if (! File::exists($source)) {
            $this->command?->warn("Image not found, skipped: {$path}");

            return null;
        }

        $target = 'content/'.$path;
        Storage::disk('public')->put($target, File::get($source));

        return $target;
    }
}
