<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Organization;
use App\Models\PublicFigure;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;

#[Name('update-achievement')]
#[Description('Partially update an achievement by slug. Only the fields you pass are changed; passing achievers or organizations re-syncs those links (an empty array clears them).')]
class UpdateAchievement extends InjazatTool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        if (! $this->can('Update:Achievement')) {
            return Response::error('You are not allowed to update achievements.');
        }

        $achievement = $this->findAchievement((string) $request->get('slug'));

        if ($achievement === null) {
            return Response::error("No achievement matches [{$request->get('slug')}].");
        }

        $validated = $request->validate([
            'slug' => ['required', 'string'],
            'new_slug' => ['nullable', 'string', 'max:255', Rule::unique('achievements', 'slug')->ignore($achievement->id)],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
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

        $updates = [];

        $map = [
            'new_slug' => 'slug',
            'title_ar' => 'title_ar',
            'title_en' => 'title_en',
            'achieved_at' => 'achieved_at',
            'country_ar' => 'country_ar',
            'country_en' => 'country_en',
            'excerpt_ar' => 'excerpt_ar',
            'excerpt_en' => 'excerpt_en',
            'description_ar' => 'description_ar',
            'description_en' => 'description_en',
            'video_url' => 'video_url',
            'is_published' => 'is_published',
            'sort_order' => 'sort_order',
        ];

        foreach ($map as $input => $column) {
            if (array_key_exists($input, $validated)) {
                $updates[$column] = $validated[$input];
            }
        }

        if (array_key_exists('scope', $validated)) {
            $updates['achievement_scope_id'] = $this->taxonomyId('achievement_scopes', $validated['scope']);
        }

        if (array_key_exists('main_type', $validated)) {
            $updates['achievement_main_type_id'] = $this->taxonomyId('achievement_main_types', $validated['main_type']);
        }

        if (array_key_exists('sub_type', $validated)) {
            $updates['sub_achievement_type_id'] = $this->taxonomyId('sub_achievement_types', $validated['sub_type']);
        }

        if ($updates !== []) {
            $achievement->update($updates);
        }

        if (array_key_exists('achievers', $validated)) {
            $achievement->publicFigures()->sync($this->pivotSyncData($validated['achievers'], PublicFigure::class));
        }

        if (array_key_exists('organizations', $validated)) {
            $achievement->organizations()->sync($this->pivotSyncData($validated['organizations'], Organization::class));
        }

        return $this->structured([
            'updated' => true,
            'achievement' => $this->achievementPayload($achievement->refresh()),
        ]);
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'slug' => $schema->string()->required()->description('Current slug of the achievement to update.'),
            'new_slug' => $schema->string()->description('Rename the slug; must stay unique.'),
            'title_ar' => $schema->string()->description('Arabic title.'),
            'title_en' => $schema->string()->description('English title.'),
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
            'is_published' => $schema->boolean()->description('Published flag.'),
            'sort_order' => $schema->integer()->description('Listing order, lower first.'),
            'achievers' => $schema->array()->items($schema->string())->description('PublicFigure slugs; re-syncs the links when passed.'),
            'organizations' => $schema->array()->items($schema->string())->description('Organization slugs; re-syncs the links when passed.'),
        ];
    }
}
