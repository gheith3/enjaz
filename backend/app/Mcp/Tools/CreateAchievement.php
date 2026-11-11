<?php

namespace App\Mcp\Tools;

use App\Models\Achievement;
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

#[Name('create-achievement')]
#[Description('Create an achievement (a draft unless is_published is true). title_ar is required; slug is auto-generated from the title when omitted. Reference scope/main_type by slug (call list-taxonomies first), and link achievers/organizations by slug arrays (the first achiever is the primary one). description_* is the story text (plain text or HTML).')]
class CreateAchievement extends InjazatTool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $validated = $request->validate([
            ...$this->achievementRules(),
            'title_ar' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', $this->uniqueSlug('achievements')],
        ]);

        if (! $this->can('Create:Achievement')) {
            return Response::error('You are not allowed to create achievements.');
        }

        $achievement = DB::transaction(function () use ($validated): Achievement {
            $achievement = Achievement::query()->create([
                'slug' => $this->newSlug($validated['slug'] ?? null, $validated['title_en'] ?? null, $validated['title_ar'], Achievement::class),
                'title' => $this->translations($validated, 'title'),
                'excerpt' => $this->translations($validated, 'excerpt') ?: null,
                'body' => $this->translations($validated, 'description', richText: true) ?: null,
                'country' => $this->translations($validated, 'country') ?: null,
                'discipline' => $this->translations($validated, 'discipline') ?: null,
                'scope_id' => $this->taxonomyId(Scope::class, $validated['scope'] ?? null),
                'field_id' => $this->taxonomyId(Field::class, $validated['main_type'] ?? null),
                'achieved_at' => $validated['achieved_at'] ?? null,
                'video_url' => $validated['video_url'] ?? null,
                'is_published' => $validated['is_published'] ?? false,
                'sort_order' => $validated['sort_order'] ?? 0,
            ]);

            if (array_key_exists('achievers', $validated)) {
                $this->syncAchievers($achievement, $validated['achievers'] ?? []);
            }

            if (array_key_exists('organizations', $validated)) {
                $this->syncOrganizations($achievement, $validated['organizations'] ?? []);
            }

            return $achievement;
        });

        return $this->structured(
            ['created' => true, 'achievement' => $this->achievementPayload($achievement->refresh())],
            $this->ignoredNotes($validated, self::IGNORED_ACHIEVEMENT),
        );
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'title_ar' => $schema->string()->required()->description('Arabic title (required).'),
            ...AchievementSchema::fields($schema),
            'slug' => $schema->string()->description('URL slug; auto-generated from the title when omitted.'),
            'is_published' => $schema->boolean()->default(false)->description('Publish immediately (default: draft).'),
            'sort_order' => $schema->integer()->default(0)->description('Listing order, lower first.'),
            'achievers' => $schema->array()->items($schema->string())->description('Achiever slugs to link; the first is the primary achiever.'),
            'organizations' => $schema->array()->items($schema->string())->description('Organization slugs to link.'),
        ];
    }
}
