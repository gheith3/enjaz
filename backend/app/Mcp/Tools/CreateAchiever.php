<?php

namespace App\Mcp\Tools;

use App\Models\Achiever;
use App\Models\Field;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Support\Facades\DB;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;

#[Name('create-achiever')]
#[Description('Create an achiever (public figure), a draft unless is_published is true. name_ar is required; slug is auto-generated from the name when omitted. Reference main_type by slug (call list-taxonomies first) and link achievements by slug array. Pass gender (m/f).')]
class CreateAchiever extends InjazatTool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $validated = $request->validate([
            ...$this->achieverRules(),
            'name_ar' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', $this->uniqueSlug('achievers')],
        ]);

        if (! $this->can('Create:Achiever')) {
            return Response::error('You are not allowed to create achievers.');
        }

        $achiever = DB::transaction(function () use ($validated): Achiever {
            $achiever = Achiever::query()->create([
                'slug' => $this->newSlug($validated['slug'] ?? null, $validated['name_en'] ?? null, $validated['name_ar'], Achiever::class),
                'name' => $this->translations($validated, 'name'),
                'gender' => $validated['gender'] ?? 'm',
                'field_id' => $this->taxonomyId(Field::class, $validated['main_type'] ?? null),
                'headline' => $this->translations($validated, 'headline') ?: null,
                'short_bio' => $this->translations($validated, 'description') ?: null,
                'story' => $this->translations($validated, 'story') ?: null,
                'is_featured' => $validated['is_featured'] ?? false,
                'show_in_home_strip' => $validated['in_home_carousel'] ?? false,
                'is_published' => $validated['is_published'] ?? false,
                'sort_order' => $validated['sort_order'] ?? 0,
            ]);

            $this->syncSocialLinks($achiever, $validated);

            if (array_key_exists('achievements', $validated)) {
                $this->syncAchievements($achiever, $validated['achievements'] ?? []);
            }

            return $achiever;
        });

        $notes = $this->ignoredNotes($validated, self::IGNORED_ACHIEVER);

        if (! isset($validated['gender'])) {
            $notes[] = '`gender` was not given and was set to "m"; call update-achiever with gender "f" if needed.';
        }

        return $this->structured(['created' => true, 'achiever' => $this->achieverPayload($achiever->refresh())], $notes);
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name_ar' => $schema->string()->required()->description('Arabic name (required).'),
            ...AchieverSchema::fields($schema),
            'slug' => $schema->string()->description('URL slug; auto-generated from the name when omitted.'),
            'is_featured' => $schema->boolean()->default(false)->description('Feature on the achievers page.'),
            'in_home_carousel' => $schema->boolean()->default(false)->description('Show in the home page strip of achievers.'),
            'is_published' => $schema->boolean()->default(false)->description('Publish immediately (default: draft).'),
            'sort_order' => $schema->integer()->default(0)->description('Listing order, lower first.'),
            'achievements' => $schema->array()->items($schema->string())->description('Achievement slugs to link.'),
        ];
    }
}
