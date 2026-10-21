<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\PublicFigure;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('search-achievers')]
#[Description('Find achievers (public figures) by full or partial Arabic/English name, main-type slug or published flag. Returns slug, name and main type; use get-achiever for the full record.')]
#[IsReadOnly]
class SearchAchievers extends InjazatTool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $request->validate([
            'q' => ['nullable', 'string'],
            'main_type' => ['nullable', 'string', 'exists:achievement_main_types,slug'],
            'published' => ['nullable', 'boolean'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        if (! $this->can('ViewAny:PublicFigure')) {
            return Response::error('You are not allowed to search achievers.');
        }

        $achievers = PublicFigure::query()
            ->with('achievementMainType')
            ->when($request->filled('main_type'), fn (Builder $query) => $query->whereHas(
                'achievementMainType',
                fn (Builder $types) => $types->where('slug', $request->get('main_type')),
            ))
            ->when(! is_null($request->get('published')), fn (Builder $query) => $query->where('is_published', $request->boolean('published')));

        $search = trim((string) $request->get('q', ''));

        if ($search !== '') {
            $like = '%'.$this->like($search).'%';
            $achievers->where(function (Builder $query) use ($like): void {
                $query->whereRaw("public_figures.name_ar LIKE ? ESCAPE '\\'", [$like])
                    ->orWhereRaw("public_figures.name_en LIKE ? ESCAPE '\\'", [$like])
                    ->orWhereRaw("public_figures.slug LIKE ? ESCAPE '\\'", [$like]);
            });
        }

        $achievers->orderBy('sort_order')->orderBy('name_ar');

        $total = (clone $achievers)->count();
        $limit = $this->limit($request, 25, 100);
        $matches = $achievers->limit($limit)->get();

        return $this->structured([
            'achievers' => $matches->map(fn (PublicFigure $figure): array => [
                'slug' => $figure->slug,
                'name_ar' => $figure->name_ar,
                'name_en' => $figure->name_en,
                'headline_ar' => $figure->headline_ar,
                'headline_en' => $figure->headline_en,
                'main_type' => $figure->achievementMainType?->slug,
                'is_featured' => $figure->is_featured,
                'is_published' => $figure->is_published,
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
            'q' => $schema->string()->description('Full or partial Arabic/English name or slug.'),
            'main_type' => $schema->string()->description('Optional main-type slug. Use list-taxonomies to discover slugs.'),
            'published' => $schema->boolean()->description('Filter by published flag.'),
            'limit' => $schema->integer()->min(1)->max(100)->default(25)->description('Maximum rows to return; total and truncated show whether more exist.'),
        ];
    }
}
