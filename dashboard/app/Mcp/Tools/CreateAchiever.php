<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Achievement;
use App\Models\PublicFigure;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;

#[Name('create-achiever')]
#[Description('Create an achiever (public figure). name_ar is required; slug is auto-generated from the name when omitted. Reference main_type by slug (call list-taxonomies first) and link achievements by slug array.')]
class CreateAchiever extends InjazatTool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:public_figures,slug'],
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

        if (! $this->can('Create:PublicFigure')) {
            return Response::error('You are not allowed to create achievers.');
        }

        $figure = PublicFigure::query()->create([
            'name_ar' => $validated['name_ar'],
            'name_en' => $validated['name_en'] ?? null,
            'slug' => $this->uniqueSlug($validated['slug'] ?? null, $validated['name_en'] ?? null, $validated['name_ar'], 'public_figures'),
            'achievement_main_type_id' => $this->taxonomyId('achievement_main_types', $validated['main_type'] ?? null),
            'headline_ar' => $validated['headline_ar'] ?? null,
            'headline_en' => $validated['headline_en'] ?? null,
            'description_ar' => $validated['description_ar'] ?? '',
            'description_en' => $validated['description_en'] ?? null,
            'story_ar' => $validated['story_ar'] ?? null,
            'story_en' => $validated['story_en'] ?? null,
            'is_featured' => $validated['is_featured'] ?? false,
            'in_home_carousel' => $validated['in_home_carousel'] ?? false,
            'is_published' => $validated['is_published'] ?? false,
            'sort_order' => $validated['sort_order'] ?? 0,
            'linkedin_url' => $validated['linkedin_url'] ?? null,
            'twitter_url' => $validated['twitter_url'] ?? null,
            'instagram_url' => $validated['instagram_url'] ?? null,
            'facebook_url' => $validated['facebook_url'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ]);

        if (array_key_exists('achievements', $validated)) {
            $figure->achievements()->sync($this->pivotSyncData($validated['achievements'], Achievement::class));
        }

        return $this->structured([
            'created' => true,
            'achiever' => $this->achieverPayload($figure),
        ]);
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name_ar' => $schema->string()->required()->description('Arabic name (required).'),
            'name_en' => $schema->string()->description('English name.'),
            'slug' => $schema->string()->description('URL slug; auto-generated from the name when omitted.'),
            'main_type' => $schema->string()->description('Main-type slug; use list-taxonomies.'),
            'headline_ar' => $schema->string()->description('Arabic headline.'),
            'headline_en' => $schema->string()->description('English headline.'),
            'description_ar' => $schema->string()->description('Arabic description.'),
            'description_en' => $schema->string()->description('English description.'),
            'story_ar' => $schema->string()->description('Arabic success story.'),
            'story_en' => $schema->string()->description('English success story.'),
            'is_featured' => $schema->boolean()->default(false)->description('Feature on the achievers page.'),
            'in_home_carousel' => $schema->boolean()->default(false)->description('Show in the home carousel.'),
            'is_published' => $schema->boolean()->default(false)->description('Publish immediately.'),
            'sort_order' => $schema->integer()->default(0)->description('Listing order, lower first.'),
            'linkedin_url' => $schema->string()->description('LinkedIn profile URL.'),
            'twitter_url' => $schema->string()->description('Twitter/X profile URL.'),
            'instagram_url' => $schema->string()->description('Instagram profile URL.'),
            'facebook_url' => $schema->string()->description('Facebook profile URL.'),
            'whatsapp_number' => $schema->string()->description('WhatsApp number.'),
            'email' => $schema->string()->description('Contact email.'),
            'phone' => $schema->string()->description('Contact phone.'),
            'achievements' => $schema->array()->items($schema->string())->description('Achievement slugs to link.'),
        ];
    }
}
