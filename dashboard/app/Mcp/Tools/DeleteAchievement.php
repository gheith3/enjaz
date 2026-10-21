<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;

#[Name('delete-achievement')]
#[Description('Soft-delete an achievement by slug. The record is hidden everywhere but stays recoverable in the dashboard.')]
#[IsDestructive]
class DeleteAchievement extends InjazatTool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $request->validate([
            'slug' => ['required', 'string'],
        ]);

        if (! $this->can('Delete:Achievement')) {
            return Response::error('You are not allowed to delete achievements.');
        }

        $achievement = $this->findAchievement((string) $request->get('slug'));

        if ($achievement === null) {
            return Response::error("No achievement matches [{$request->get('slug')}].");
        }

        $achievement->delete();

        return $this->structured([
            'deleted' => true,
            'slug' => $achievement->slug,
        ]);
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'slug' => $schema->string()->required()->description('Slug of the achievement to soft-delete.'),
        ];
    }
}
