<?php

namespace App\Mcp\Tools;

use App\Models\Field;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Support\Facades\DB;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;

#[Name('update-achiever')]
#[Description('Partially update an achiever (public figure) by slug. Only the fields you pass are changed (null or "" clears a text or social link); passing achievements re-syncs those links (an empty array clears them).')]
class UpdateAchiever extends InjazatTool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        if (! $this->can('Update:Achiever')) {
            return Response::error('You are not allowed to update achievers.');
        }

        $achiever = $this->findAchiever((string) $request->get('slug'));

        if ($achiever === null) {
            return Response::error("No achiever matches [{$request->get('slug')}].");
        }

        $validated = $request->validate([
            ...$this->achieverRules(),
            'slug' => ['required', 'string'],
            'new_slug' => ['nullable', 'string', 'max:255', $this->uniqueSlug('achievers', $achiever)],
            'name_ar' => ['sometimes', 'filled', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($achiever, $validated): void {
            $this->applyTranslations($achiever, $validated, [
                'name' => 'name', 'headline' => 'headline', 'short_bio' => 'description', 'story' => 'story',
            ]);

            // Old argument => [column, value when null is passed].
            foreach ([
                'is_featured' => ['is_featured', false], 'in_home_carousel' => ['show_in_home_strip', false],
                'is_published' => ['is_published', false], 'sort_order' => ['sort_order', 0],
            ] as $input => [$column, $default]) {
                if (array_key_exists($input, $validated)) {
                    $achiever->{$column} = $validated[$input] ?? $default;
                }
            }

            if (! empty($validated['gender'])) {
                $achiever->gender = $validated['gender'];
            }

            if (! empty($validated['new_slug'])) {
                $achiever->slug = $validated['new_slug'];
            }

            if (array_key_exists('main_type', $validated)) {
                $achiever->field_id = $this->taxonomyId(Field::class, $validated['main_type']);
            }

            $achiever->save();
            $this->syncSocialLinks($achiever, $validated);

            if (array_key_exists('achievements', $validated)) {
                $this->syncAchievements($achiever, $validated['achievements'] ?? []);
            }
        });

        return $this->structured(
            ['updated' => true, 'achiever' => $this->achieverPayload($achiever->refresh())],
            $this->ignoredNotes($validated, self::IGNORED_ACHIEVER),
        );
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
            ...AchieverSchema::fields($schema),
            'is_featured' => $schema->boolean()->description('Featured flag.'),
            'in_home_carousel' => $schema->boolean()->description('Home page strip flag.'),
            'is_published' => $schema->boolean()->description('Published flag.'),
            'sort_order' => $schema->integer()->description('Listing order, lower first.'),
            'achievements' => $schema->array()->items($schema->string())->description('Achievement slugs; re-syncs the links when passed.'),
        ];
    }
}
