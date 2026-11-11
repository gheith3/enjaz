<?php

namespace App\Mcp\Tools;

use App\Models\Achievement;
use App\Support\ArabicSearch;
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
            'scope' => ['nullable', 'string', 'exists:scopes,slug'],
            'main_type' => ['nullable', 'string', 'exists:fields,slug'],
            'published' => ['nullable', 'boolean'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        if (! $this->can('ViewAny:Achievement')) {
            return Response::error('You are not allowed to search achievements.');
        }

        $achievements = Achievement::query()
            ->with(['scope', 'field'])
            ->when($request->filled('scope'), fn (Builder $query) => $query->whereHas('scope', fn (Builder $scopes) => $scopes->where('slug', $request->get('scope'))))
            ->when($request->filled('main_type'), fn (Builder $query) => $query->whereHas('field', fn (Builder $fields) => $fields->where('slug', $request->get('main_type'))))
            ->when(! is_null($request->get('published')), fn (Builder $query) => $query->where('is_published', $request->boolean('published')));

        $search = trim((string) $request->get('q', ''));

        if ($search !== '') {
            $achievements->where(fn (Builder $query) => ArabicSearch::where($query, ['title'], $search)
                ->orWhere('slug', 'ilike', '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search).'%'));
        }

        $achievements->orderByRaw('achieved_at desc nulls last')->orderBy('sort_order')->orderBy('id');

        $total = (clone $achievements)->count();
        $matches = $achievements->limit($this->limit($request, 25, 100))->get();

        return $this->structured([
            'achievements' => $matches->map(fn (Achievement $achievement): array => [
                'slug' => $achievement->slug,
                'title_ar' => $this->translation($achievement, 'title', 'ar'),
                'title_en' => $this->translation($achievement, 'title', 'en'),
                'main_type' => $achievement->field?->slug,
                'scope' => $achievement->scope?->slug,
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
