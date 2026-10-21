<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Achievement;
use App\Models\Organization;
use App\Models\PublicFigure;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;

#[Name('create-achievement')]
#[Description('Create an achievement. title_ar is required; slug is auto-generated from the title when omitted. Reference scope/main_type/sub_type by slug (call list-taxonomies first), and link achievers/organizations by slug arrays.')]
class CreateAchievement extends InjazatTool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $validated = $request->validate([
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:achievements,slug'],
            'scope' => ['nullable', 'string', 'exists:achievement_scopes,slug'],
            'main_type' => ['nullable', 'string', 'exists:achievement_main_types,slug'],
            'sub_type' => ['nullable', 'string', 'exists:sub_achievement_types,slug'],
            'achieved_at' => ['nullable', 'date'],
            'country_ar' => ['nullable', 'string', 'max:255'],
            'country_en' => ['nullable', 'string', 'max:255'],
            'excerpt_ar' => ['nullable', 'string'],
            'excerpt_en' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
            'achievers' => ['nullable', 'array'],
            'achievers.*' => ['string', 'exists:public_figures,slug'],
            'organizations' => ['nullable', 'array'],
            'organizations.*' => ['string', 'exists:organizations,slug'],
        ]);

        if (! $this->can('Create:Achievement')) {
            return Response::error('You are not allowed to create achievements.');
        }

        $achievement = Achievement::query()->create([
            'title_ar' => $validated['title_ar'],
            'title_en' => $validated['title_en'] ?? null,
            'slug' => $this->uniqueSlug($validated['slug'] ?? null, $validated['title_en'] ?? null, $validated['title_ar'], 'achievements'),
            'achievement_scope_id' => $this->taxonomyId('achievement_scopes', $validated['scope'] ?? null),
            'achievement_main_type_id' => $this->taxonomyId('achievement_main_types', $validated['main_type'] ?? null),
            'sub_achievement_type_id' => $this->taxonomyId('sub_achievement_types', $validated['sub_type'] ?? null),
            'achieved_at' => $validated['achieved_at'] ?? null,
            'country_ar' => $validated['country_ar'] ?? null,
            'country_en' => $validated['country_en'] ?? null,
            'excerpt_ar' => $validated['excerpt_ar'] ?? null,
            'excerpt_en' => $validated['excerpt_en'] ?? null,
            // description_ar is NOT NULL in the schema.
            'description_ar' => $validated['description_ar'] ?? '',
            'description_en' => $validated['description_en'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'is_published' => $validated['is_published'] ?? false,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        if (array_key_exists('achievers', $validated)) {
            $achievement->publicFigures()->sync($this->pivotSyncData($validated['achievers'], PublicFigure::class));
        }

        if (array_key_exists('organizations', $validated)) {
            $achievement->organizations()->sync($this->pivotSyncData($validated['organizations'], Organization::class));
        }

        return $this->structured([
            'created' => true,
            'achievement' => $this->achievementPayload($achievement),
        ]);
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'title_ar' => $schema->string()->required()->description('Arabic title (required).'),
            'title_en' => $schema->string()->description('English title.'),
            'slug' => $schema->string()->description('URL slug; auto-generated from the title when omitted.'),
            'scope' => $schema->string()->description('Scope slug; use list-taxonomies.'),
            'main_type' => $schema->string()->description('Main-type slug; use list-taxonomies.'),
            'sub_type' => $schema->string()->description('Sub-type slug; use list-taxonomies.'),
            'achieved_at' => $schema->string()->description('Date of the achievement, YYYY-MM-DD.'),
            'country_ar' => $schema->string()->description('Country name in Arabic.'),
            'country_en' => $schema->string()->description('Country name in English.'),
            'excerpt_ar' => $schema->string()->description('Short Arabic excerpt.'),
            'excerpt_en' => $schema->string()->description('Short English excerpt.'),
            'description_ar' => $schema->string()->description('Full Arabic description.'),
            'description_en' => $schema->string()->description('Full English description.'),
            'video_url' => $schema->string()->description('Optional video URL.'),
            'is_published' => $schema->boolean()->default(false)->description('Publish immediately.'),
            'sort_order' => $schema->integer()->default(0)->description('Listing order, lower first.'),
            'achievers' => $schema->array()->items($schema->string())->description('PublicFigure slugs to link.'),
            'organizations' => $schema->array()->items($schema->string())->description('Organization slugs to link.'),
        ];
    }
}
