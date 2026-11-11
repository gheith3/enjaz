<?php

namespace App\Mcp\Tools;

use App\Models\Field;
use App\Models\Scope;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Support\Facades\DB;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;

#[Name('update-achievement')]
#[Description('Partially update an achievement by slug. Only the fields you pass are changed (null or "" clears a text); passing achievers or organizations re-syncs those links (an empty array clears them).')]
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
            ...$this->achievementRules(),
            'slug' => ['required', 'string'],
            'new_slug' => ['nullable', 'string', 'max:255', $this->uniqueSlug('achievements', $achievement)],
            'title_ar' => ['sometimes', 'filled', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($achievement, $validated): void {
            $this->applyTranslations($achievement, $validated, [
                'title' => 'title', 'excerpt' => 'excerpt', 'body' => 'description', 'country' => 'country', 'discipline' => 'discipline',
            ], richText: ['body']);

            // Plain columns: argument => value when null is passed.
            foreach (['achieved_at' => null, 'video_url' => null, 'is_published' => false, 'sort_order' => 0] as $column => $default) {
                if (array_key_exists($column, $validated)) {
                    $achievement->{$column} = $validated[$column] ?? $default;
                }
            }

            if (! empty($validated['new_slug'])) {
                $achievement->slug = $validated['new_slug'];
            }

            if (array_key_exists('scope', $validated)) {
                $achievement->scope_id = $this->taxonomyId(Scope::class, $validated['scope']);
            }

            if (array_key_exists('main_type', $validated)) {
                $achievement->field_id = $this->taxonomyId(Field::class, $validated['main_type']);
            }

            $achievement->save();

            if (array_key_exists('achievers', $validated)) {
                $this->syncAchievers($achievement, $validated['achievers'] ?? []);
            }

            if (array_key_exists('organizations', $validated)) {
                $this->syncOrganizations($achievement, $validated['organizations'] ?? []);
            }
        });

        return $this->structured(
            ['updated' => true, 'achievement' => $this->achievementPayload($achievement->refresh())],
            $this->ignoredNotes($validated, self::IGNORED_ACHIEVEMENT),
        );
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
            ...AchievementSchema::fields($schema),
            'is_published' => $schema->boolean()->description('Published flag.'),
            'sort_order' => $schema->integer()->description('Listing order, lower first.'),
            'achievers' => $schema->array()->items($schema->string())->description('Achiever slugs; re-syncs the links when passed (the first is the primary achiever).'),
            'organizations' => $schema->array()->items($schema->string())->description('Organization slugs; re-syncs the links when passed.'),
        ];
    }
}
