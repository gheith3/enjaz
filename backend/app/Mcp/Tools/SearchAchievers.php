<?php

namespace App\Mcp\Tools;

use App\Models\Achiever;
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

#[Name('search-achievers')]
#[Description('Find achievers (public figures) by full or partial Arabic/English name, main-type slug or published flag. Returns slug, name and main type; use get-achiever for the full record.')]
#[IsReadOnly]
class SearchAchievers extends InjazatTool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $request->validate([
            'q' => ['nullable', 'string'],
            'main_type' => ['nullable', 'string', 'exists:fields,slug'],
            'published' => ['nullable', 'boolean'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        if (! $this->can('ViewAny:Achiever')) {
            return Response::error('You are not allowed to search achievers.');
        }

        $achievers = Achiever::query()
            ->with('field')
            ->when($request->filled('main_type'), fn (Builder $query) => $query->whereHas('field', fn (Builder $fields) => $fields->where('slug', $request->get('main_type'))))
            ->when(! is_null($request->get('published')), fn (Builder $query) => $query->where('is_published', $request->boolean('published')));

        $search = trim((string) $request->get('q', ''));

        if ($search !== '') {
            $achievers->where(fn (Builder $query) => ArabicSearch::where($query, ['name'], $search)
                ->orWhere('slug', 'ilike', '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search).'%'));
        }

        $achievers->orderBy('sort_order')->orderByRaw("name->>'ar'")->orderBy('id');

        $total = (clone $achievers)->count();
        $matches = $achievers->limit($this->limit($request, 25, 100))->get();

        return $this->structured([
            'achievers' => $matches->map(fn (Achiever $achiever): array => [
                'slug' => $achiever->slug,
                'name_ar' => $this->translation($achiever, 'name', 'ar'),
                'name_en' => $this->translation($achiever, 'name', 'en'),
                'headline_ar' => $this->translation($achiever, 'headline', 'ar'),
                'headline_en' => $this->translation($achiever, 'headline', 'en'),
                'main_type' => $achiever->field?->slug,
                'is_featured' => $achiever->is_featured,
                'is_published' => $achiever->is_published,
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
