<?php

namespace App\Mcp\Tools;

use App\Models\Field;
use App\Models\Scope;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

/**
 * Scopes (المستوى) and main types (المجالات, App\Models\Field). This app has no sub types: every main type carries
 * an empty `sub_types` list so the old shape is kept.
 */
#[Name('list-taxonomies')]
#[Description('List the scopes, main types and sub types (id, slug, title_ar, title_en) used to fill the taxonomy fields of achievements and achievers. Call this before creating or updating records; reference taxonomies by slug. Main types are the site\'s fields (المجالات); there are no sub types (sub_types is always empty: use discipline_ar/discipline_en on achievements for the specialty).')]
#[IsReadOnly]
class ListTaxonomies extends InjazatTool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        if (! $this->can('ViewAny:Achievement') && ! $this->can('ViewAny:Achiever')) {
            return Response::error('You are not allowed to list taxonomies.');
        }

        return $this->structured([
            'scopes' => Scope::query()->orderBy('sort_order')->orderBy('id')->get()
                ->map(fn (Scope $scope) => $this->taxonomy($scope))->all(),
            'main_types' => Field::query()->orderBy('sort_order')->orderBy('id')->get()
                ->map(fn (Field $field) => [...$this->taxonomy($field), 'sub_types' => []])->all(),
        ]);
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
