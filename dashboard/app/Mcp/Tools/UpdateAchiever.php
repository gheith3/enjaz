<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Achievement;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;

#[Name('update-achiever')]
#[Description('Partially update an achiever (public figure) by slug. Only the fields you pass are changed; passing achievements re-syncs those links (an empty array clears them).')]
class UpdateAchiever extends InjazatTool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        if (! $this->can('Update:PublicFigure')) {
            return Response::error('You are not allowed to update achievers.');
        }

        $figure = $this->findAchiever((string) $request->get('slug'));

        if ($figure === null) {
            return Response::error("No achiever matches [{$request->get('slug')}].");
        }

        $validated = $request->validate([
            'slug' => ['required', 'string'],
            'new_slug' => ['nullable', 'string', 'max:255', Rule::unique('public_figures', 'slug')->ignore($figure->id)],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'main_type' => ['nullable', 'string', 'exists:achievement_main_types,slug'],
            'headline_ar' => ['nullable', 'string', 'max:255'],
            'headline_en' => ['nullable', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'story_ar' => ['nullable', 'string'],
            'story_en' => ['nullable', 'string'],
            'is_featured' => ['nullable', 'boolean'],
            'in_home_carousel' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'whatsapp_number' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'achievements' => ['nullable', 'array'],
            'achievements.*' => ['string', 'exists:achievements,slug'],
        ]);

        $updates = [];

        $map = [
            'new_slug' => 'slug',
            'name_ar' => 'name_ar',
            'name_en' => 'name_en',
            'headline_ar' => 'headline_ar',
            'headline_en' => 'headline_en',
            'description_ar' => 'description_ar',
            'description_en' => 'description_en',
            'story_ar' => 'story_ar',
            'story_en' => 'story_en',
            'is_featured' => 'is_featured',
            'in_home_carousel' => 'in_home_carousel',
            'is_published' => 'is_published',
            'sort_order' => 'sort_order',
            'linkedin_url' => 'linkedin_url',
            'twitter_url' => 'twitter_url',
            'instagram_url' => 'instagram_url',
            'facebook_url' => 'facebook_url',
            'whatsapp_number' => 'whatsapp_number',
            'email' => 'email',
            'phone' => 'phone',
        ];

        foreach ($map as $input => $column) {
            if (array_key_exists($input, $validated)) {
                $updates[$column] = $validated[$input];
            }
        }

        if (array_key_exists('main_type', $validated)) {
            $updates['achievement_main_type_id'] = $this->taxonomyId('achievement_main_types', $validated['main_type']);
        }

        if ($updates !== []) {
            $figure->update($updates);
        }

        if (array_key_exists('achievements', $validated)) {
            $figure->achievements()->sync($this->pivotSyncData($validated['achievements'], Achievement::class));
        }

        return $this->structured([
            'updated' => true,
            'achiever' => $this->achieverPayload($figure->refresh()),
        ]);
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'slug' => $schema->string()->required()->description('Current slug of the achiever to update.'),
            'new_slug' => $schema->string()->description('Rename the slug; must stay unique.'),
            'name_ar' => $schema->string()->description('Arabic name.'),
            'name_en' => $schema->string()->description('English name.'),
            'main_type' => $schema->string()->description('Main-type slug; use list-taxonomies.'),
            'headline_ar' => $schema->string()->description('Arabic headline.'),
            'headline_en' => $schema->string()->description('English headline.'),
            'description_ar' => $schema->string()->description('Arabic description.'),
            'description_en' => $schema->string()->description('English description.'),
            'story_ar' => $schema->string()->description('Arabic success story.'),
            'story_en' => $schema->string()->description('English success story.'),
            'is_featured' => $schema->boolean()->description('Featured flag.'),
            'in_home_carousel' => $schema->boolean()->description('Home carousel flag.'),
            'is_published' => $schema->boolean()->description('Published flag.'),
            'sort_order' => $schema->integer()->description('Listing order, lower first.'),
            'linkedin_url' => $schema->string()->description('LinkedIn profile URL.'),
            'twitter_url' => $schema->string()->description('Twitter/X profile URL.'),
            'instagram_url' => $schema->string()->description('Instagram profile URL.'),
            'facebook_url' => $schema->string()->description('Facebook profile URL.'),
            'whatsapp_number' => $schema->string()->description('WhatsApp number.'),
            'email' => $schema->string()->description('Contact email.'),
            'phone' => $schema->string()->description('Contact phone.'),
            'achievements' => $schema->array()->items($schema->string())->description('Achievement slugs; re-syncs the links when passed.'),
        ];
    }
}
