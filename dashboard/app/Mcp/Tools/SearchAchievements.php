<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Achievement;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('search-achievements')]
#[Description('Find achievements by full or partial Arabic/English title, scope slug, main-type slug or published flag. Returns slug, title, type, scope and achieved_at; use get-achievement for the full record.')]
#[IsReadOnly]
class SearchAchievements extends InjazatTool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $request->validate([
            'q' => ['nullable', 'string'],
            'scope' => ['nullable', 'string', 'exists:achievement_scopes,slug'],
            'main_type' => ['nullable', 'string', 'exists:achievement_main_types,slug'],
            'published' => ['nullable', 'boolean'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        if (! $this->can('ViewAny:Achievement')) {
            return Response::error('You are not allowed to search achievements.');
        }

        $achievements = Achievement::query()
            ->with(['achievementScope', 'achievementMainType'])
            ->when($request->filled('scope'), fn (Builder $query) => $query->whereHas(
                'achievementScope',
                fn (Builder $scopes) => $scopes->where('slug', $request->get('scope')),
            ))
            ->when($request->filled('main_type'), fn (Builder $query) => $query->whereHas(
                'achievementMainType',
                fn (Builder $types) => $types->where('slug', $request->get('main_type')),
            ))
            ->when(! is_null($request->get('published')), fn (Builder $query) => $query->where('is_published', $request->boolean('published')));

        $search = trim((string) $request->get('q', ''));

        if ($search !== '') {
            $like = '%'.$this->like($search).'%';
            $achievements->where(function (Builder $query) use ($like): void {
                $query->whereRaw("achievements.title_ar LIKE ? ESCAPE '\\'", [$like])
                    ->orWhereRaw("achievements.title_en LIKE ? ESCAPE '\\'", [$like])
                    ->orWhereRaw("achievements.slug LIKE ? ESCAPE '\\'", [$like]);
            });
        }

        $achievements->orderByDesc('achieved_at')->orderBy('sort_order');

        $total = (clone $achievements)->count();
        $limit = $this->limit($request, 25, 100);
        $matches = $achievements->limit($limit)->get();

        return $this->structured([
            'achievements' => $matches->map(fn (Achievement $achievement): array => [
                'slug' => $achievement->slug,
                'title_ar' => $achievement->title_ar,
                'title_en' => $achievement->title_en,
                'main_type' => $achievement->achievementMainType?->slug,
                'scope' => $achievement->achievementScope?->slug,
                'is_published' => $achievement->is_published,
                'achieved_at' => $achievement->achieved_at?->toDateString(),
            ])->values()->all(),
            'total' => $total,
            'truncated' => $total > $matches->count(),
        ]);
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'q' => $schema->string()->description('Full or partial Arabic/English title or slug.'),
            'scope' => $schema->string()->description('Optional scope slug. Use list-taxonomies to discover slugs.'),
            'main_type' => $schema->string()->description('Optional main-type slug.'),
            'published' => $schema->boolean()->description('Filter by published flag.'),
            'limit' => $schema->integer()->min(1)->max(100)->default(25)->description('Maximum rows to return; total and truncated show whether more exist.'),
        ];
    }
}
