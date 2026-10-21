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

#[Name('delete-achiever')]
#[Description('Soft-delete an achiever (public figure) by slug. The record is hidden everywhere but stays recoverable in the dashboard.')]
#[IsDestructive]
class DeleteAchiever extends InjazatTool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $request->validate([
            'slug' => ['required', 'string'],
        ]);

        if (! $this->can('Delete:PublicFigure')) {
            return Response::error('You are not allowed to delete achievers.');
        }

        $figure = $this->findAchiever((string) $request->get('slug'));

        if ($figure === null) {
            return Response::error("No achiever matches [{$request->get('slug')}].");
        }

        $figure->delete();

        return $this->structured([
            'deleted' => true,
            'slug' => $figure->slug,
        ]);
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'slug' => $schema->string()->required()->description('Slug of the achiever to soft-delete.'),
        ];
    }
}
