<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\AchievementMainType;
use App\Models\AchievementScope;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Support\Collection;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('list-taxonomies')]
#[Description('List the scopes, main types and sub types (id, slug, title_ar, title_en) used to fill the taxonomy fields of achievements and achievers. Call this before creating or updating records; reference taxonomies by slug.')]
#[IsReadOnly]
class ListTaxonomies extends InjazatTool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        if (! $this->can('ViewAny:Achievement') && ! $this->can('ViewAny:PublicFigure')) {
            return Response::error('You are not allowed to list taxonomies.');
        }

        return $this->structured([
            'scopes' => $this->rows(AchievementScope::query()->ordered()->get()),
            'main_types' => AchievementMainType::query()->ordered()->get()->map(fn (AchievementMainType $type): array => [
                ...$this->row($type),
                'sub_types' => $this->rows($type->subAchievementTypes()->ordered()->get()),
            ])->all(),
        ]);
    }

    /**
     * @param  Collection<int, Model>  $models
     * @return list<array<string, mixed>>
     */
    private function rows(Collection $models): array
    {
        return $models->map(fn (Model $model): array => $this->row($model))->all();
    }

    /** @return array<string, mixed> */
    private function row(Model $model): array
    {
        return [
            'id' => $model->getKey(),
            'slug' => $model->getAttribute('slug'),
            'title_ar' => $model->getAttribute('title_ar'),
            'title_en' => $model->getAttribute('title_en'),
        ];
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
