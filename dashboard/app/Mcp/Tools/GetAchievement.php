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
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('get-achievement')]
#[Description('Get one achievement by slug or id: the full bilingual record with its scope, main and sub type, linked achiever slugs and organization slugs.')]
#[IsReadOnly]
class GetAchievement extends InjazatTool
{
    public function handle(Request $request): Response|ResponseFactory
    {
        $request->validate([
            'identifier' => ['required', 'string'],
        ]);

        if (! $this->can('View:Achievement')) {
            return Response::error('You are not allowed to view achievements.');
        }

        $achievement = $this->findAchievement((string) $request->get('identifier'));

        if ($achievement === null) {
            return Response::error("No achievement matches [{$request->get('identifier')}].");
        }

        return $this->structured([
            'achievement' => $this->achievementPayload($achievement),
        ]);
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'identifier' => $schema->string()->required()->description('Achievement slug or UUID.'),
        ];
    }
}
